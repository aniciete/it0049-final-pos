#!/usr/bin/env python3
"""
Automated FTPS Deployment for Bright-Market Laravel POS to InfinityFree.
Reads credentials from environment variables or gitignored .deploy.env / .env.production,
builds the lean zip, and uploads it0049-final-pos-infinityfree.zip, extract.php, and a
one-time .extract_token over FTPS.
"""

import ftplib
import io
import os
import secrets
import subprocess
import sys
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


def deploy() -> None:
    creds = load_credentials()
    build_package()

    host = creds["FTP_HOST"]
    port = int(creds["FTP_PORT"])
    user = creds["FTP_USER"]
    password = creds["FTP_PASS"]
    app_url = creds["APP_URL"]
    allow_insecure = os.getenv("ALLOW_INSECURE_FTP") == "1"

    if not ZIP_PATH.exists() or not EXTRACT_PHP.exists():
        raise RuntimeError("Deployment artifacts missing.")

    print(f"Connecting to FTPS {host}:{port} as {user} ...")

    ftp: ftplib.FTP | None = None
    try:
        try:
            ftps = ftplib.FTP_TLS(timeout=30)
            ftps.connect(host, port)
            ftps.auth()
            ftps.prot_p()
            ftps.login(user, password)
            ftp = ftps
            print("FTPS (TLS) connected and authenticated.")
        except Exception as tls_exc:
            if not allow_insecure:
                raise RuntimeError(
                    f"FTPS TLS connection failed ({tls_exc}). Refusing cleartext FTP fallback without ALLOW_INSECURE_FTP=1."
                ) from tls_exc
            plain = ftplib.FTP(timeout=30)
            plain.connect(host, port)
            plain.login(user, password)
            ftp = plain

        ftp.set_pasv(True)
        ftp.cwd("/htdocs")

        htaccess_path = BASE_DIR / ".htaccess"
        if htaccess_path.exists():
            with htaccess_path.open("rb") as f:
                ftp.storbinary("STOR .htaccess", f)

        token = secrets.token_urlsafe(24)
        ftp.storbinary("STOR .extract_token", io.BytesIO(token.encode("ascii")))

        with ZIP_PATH.open("rb") as f:
            ftp.storbinary(f"STOR {ZIP_PATH.name}", f, blocksize=262144)

        with EXTRACT_PHP.open("rb") as f:
            ftp.storbinary("STOR extract.php", f)

        print("\n========================================================")
        print(" DEPLOYMENT UPLOAD COMPLETE")
        print(f" Next step: Trigger extraction at {app_url}/extract.php?token={token}")
        print("========================================================")
    finally:
        if ftp is not None:
            try:
                ftp.quit()
            except Exception:
                pass


if __name__ == "__main__":
    try:
        deploy()
    except Exception as exc:
        print(f"DEPLOYMENT ERROR: {exc}", file=sys.stderr)
        sys.exit(1)
