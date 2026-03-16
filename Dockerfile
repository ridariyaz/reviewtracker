FROM python:3.13-slim

WORKDIR /app

# Install system dependencies (libpq for Postgres, build tools for some wheels)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev build-essential \
    && rm -rf /var/lib/apt/lists/*

# Install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

ENV PORT=8080

CMD ["gunicorn", "app:app", "--bind", "0.0.0.0:$PORT"]

