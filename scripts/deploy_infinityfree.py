#!/usr/bin/env python3
"""
Automated FTPS Deployment for Bright-Market Laravel POS to InfinityFree.
Reads credentials from environment variables or gitignored .deploy.env / .env.production,
builds the lean zip, and uploads it0049-final-pos-infinityfree.zip, extract.php, and a
one-time .extract_token over FTPS using curl (TLS 1.3 session reuse + generous timeout).
"""

import os
import secrets
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent
ZIP_PATH = BASE_DIR / "it0049-final-pos-infinityfree.zip"
EXTRACT_PHP = BASE_DIR / "extract.php"
DEPLOY_ENV = BASE_DIR / ".deploy.env"
ENV_PROD = BASE_DIR / ".env.production"


def parse_dotenv(path: Path) -> dict[str, str]:
    values: dict[str, str] = {}
    if not path.exists():
        return values
    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, val = line.split("=", 1)
        values[key.strip()] = val.strip().strip('"').strip("'")
    return values


def load_credentials() -> dict[str, str]:
    deploy_vars = parse_dotenv(DEPLOY_ENV)
    prod_vars = parse_dotenv(ENV_PROD)

    ftp_user = os.getenv("FTP_USER") or deploy_vars.get("FTP_USER") or prod_vars.get("DB_USERNAME", "")
    ftp_pass = os.getenv("FTP_PASS") or deploy_vars.get("FTP_PASS") or prod_vars.get("DB_PASSWORD", "")
    app_url = os.getenv("APP_URL") or deploy_vars.get("APP_URL") or prod_vars.get("APP_URL", "")

    if not ftp_user or not ftp_pass or ftp_pass == "REPLACE_WITH_VPANEL_PASSWORD":
        raise RuntimeError(
            "Missing FTP credentials. Set FTP_USER/FTP_PASS in environment or gitignored .deploy.env."
        )

    return {
        "FTP_HOST": os.getenv("FTP_HOST") or deploy_vars.get("FTP_HOST", "ftpupload.net"),
        "FTP_PORT": os.getenv("FTP_PORT") or deploy_vars.get("FTP_PORT", "21"),
        "FTP_USER": ftp_user,
        "FTP_PASS": ftp_pass,
        "APP_URL": app_url.rstrip("/"),
    }


def build_package() -> None:
    print("==> Packaging lean production build via build-infinityfree-zip.sh ...")
    res = subprocess.run([str(BASE_DIR / "build-infinityfree-zip.sh")], cwd=str(BASE_DIR))
    if res.returncode != 0:
        raise RuntimeError("Build script failed.")


def curl_upload(creds: dict[str, str], local_file: Path, remote_name: str) -> None:
    url = f"ftp://{creds['FTP_HOST']}:{creds['FTP_PORT']}/htdocs/{remote_name}"
    config_text = f'user = "{creds["FTP_USER"]}:{creds["FTP_PASS"]}"\n'
    res = subprocess.run(
        [
            "curl",
            "--fail",
            "--show-error",
            "--silent",
            "--ssl-reqd",
            "--ftp-pasv",
            "--max-time",
            "600",
            "--config",
            "-",
            "-T",
            str(local_file),
            url,
        ],
        input=config_text,
        text=True,
    )
    if res.returncode != 0:
        raise RuntimeError(f"FTPS upload failed for {remote_name} (curl exit {res.returncode}).")


def deploy() -> None:
    if not shutil.which("curl"):
        raise RuntimeError("curl is required for FTPS deployment.")

    creds = load_credentials()
    build_package()

    if not ZIP_PATH.exists() or not EXTRACT_PHP.exists():
        raise RuntimeError("Deployment artifacts missing.")

    token = secrets.token_urlsafe(24)

    with tempfile.NamedTemporaryFile("w", encoding="ascii", delete=False) as tmp:
        tmp.write(token)
        token_tmp_path = Path(tmp.name)

    try:
        htaccess_path = BASE_DIR / ".htaccess"
        if htaccess_path.exists():
            print("==> Uploading .htaccess ...")
            curl_upload(creds, htaccess_path, ".htaccess")

        print("==> Uploading one-time .extract_token ...")
        curl_upload(creds, token_tmp_path, ".extract_token")

        size_mb = ZIP_PATH.stat().st_size / (1024 * 1024)
        print(f"==> Uploading {ZIP_PATH.name} ({size_mb:.2f} MB) via FTPS ...")
        curl_upload(creds, ZIP_PATH, ZIP_PATH.name)

        print("==> Uploading extract.php ...")
        curl_upload(creds, EXTRACT_PHP, "extract.php")
    finally:
        token_tmp_path.unlink(missing_ok=True)

    app_url = creds["APP_URL"]
    print("\n========================================================")
    print(" DEPLOYMENT UPLOAD COMPLETE")
    print(f" Next step: Trigger extraction at {app_url}/extract.php?token={token}")
    print("========================================================")


if __name__ == "__main__":
    try:
        deploy()
    except Exception as exc:
        print(f"DEPLOYMENT ERROR: {exc}", file=sys.stderr)
        sys.exit(1)
