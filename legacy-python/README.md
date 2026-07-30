# Archived Legacy Python (Flask) Application

This directory contains the original Python Flask implementation of **ReviewTracker**. 

> **Note**: The active application running in production is built on **Laravel (PHP 8.4)** located in the project root. This Python code is kept archived for reference and historical parity checks.

## Directory Structure
- `app.py`: Flask application server containing database setup, route handlers, authentication, CSV exports, and QR code logic.
- `requirements.txt`: Dependencies for the Flask app (`Flask`, `Pillow`, `colorthief`, `qrcode`, `gunicorn`, etc.).
- `templates/`: Jinja2 HTML templates for the legacy Flask interface.
- `static/`: Static assets (CSS, uploaded company logos, generated QR code PNGs).

## Running Legacy Python App Locally (Optional)

```bash
cd legacy-python
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python app.py
```
*(The server will start on `http://127.0.0.1:5000`)*
