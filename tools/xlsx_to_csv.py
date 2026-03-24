"""
xlsx_to_csv.py
==============
Converts the Bulgarian social services XLSX registry into a CSV ready for
the Social Services Directory WordPress plugin importer.

Usage:
    python tools/xlsx_to_csv.py

Input:  registar-na-dostavchicite-na-socialni-uslugi-publichen-kam-31.01.2026.xlsx
        (must be in the plugin root directory)
Output: tools/social_services_import.csv

Column mapping (Bulgarian → plugin CSV column):
  Доставчик                          → provider_name
  ЕИК                                → eik
  Община                             → municipality
  Населено място                     → settlement
  Седалище и адрес на управление     → address
  Социална услуга (all rows)         → social_service  (semicolon-separated)
  Целева група (first row)           → target_group
  Лиценз - номер и дата на издаване  → license_number  (semicolon-separated across services)
  Лиценз - дата на валидност         → license_validity (from main row)
  Лиценз с промяна - номер и дата    → license_modified_number
  Лиценз с промяна - дата валидност  → license_modified_validity
  Подновен лиценз № и дата           → license_renewed_number
  Подновен лиценз - дата валидност   → license_renewed_validity
  Установени нарушения               → violations      (concatenated from all rows)
"""

import csv
import os
import sys
from datetime import datetime

try:
    import openpyxl
except ImportError:
    sys.exit("Missing dependency: run  pip install openpyxl")

# ── Paths ────────────────────────────────────────────────────────────────────
SCRIPT_DIR  = os.path.dirname(os.path.abspath(__file__))
PLUGIN_ROOT = os.path.dirname(SCRIPT_DIR)
XLSX_FILE   = os.path.join(PLUGIN_ROOT,
    "registar-na-dostavchicite-na-socialni-uslugi-publichen-kam-31.01.2026.xlsx")
OUT_CSV     = os.path.join(SCRIPT_DIR, "social_services_import.csv")

# ── Helpers ───────────────────────────────────────────────────────────────────
def fmt_date(value):
    """Return a normalised date string or the raw value if already a string."""
    if value is None:
        return ""
    if isinstance(value, datetime):
        return value.strftime("%d.%m.%Y г.")
    return str(value).strip()

def clean(value):
    """Strip whitespace and normalise inner newlines to spaces."""
    if value is None:
        return ""
    return " ".join(str(value).split())

# ── Load workbook ─────────────────────────────────────────────────────────────
print(f"Reading: {XLSX_FILE}")
wb = openpyxl.load_workbook(XLSX_FILE, read_only=True, data_only=True)
ws = wb["Sheet1"]
all_rows = list(ws.iter_rows(min_row=2, values_only=True))  # skip header row
wb.close()
print(f"Total rows (excl. header): {len(all_rows)}")

# ── Group rows by provider ────────────────────────────────────────────────────
# The main row has a № in column 0; continuation rows have None.
providers = []      # list of {"main": row, "extra": [rows]}
for row in all_rows:
    if row[0] is not None:          # new provider
        providers.append({"main": row, "extra": []})
    elif providers:                 # continuation of previous provider
        providers[-1]["extra"].append(row)

print(f"Unique providers: {len(providers)}")

# ── Build CSV rows ────────────────────────────────────────────────────────────
CSV_COLUMNS = [
    "provider_name",
    "eik",
    "municipality",
    "settlement",
    "address",
    "social_service",
    "target_group",
    "license_number",
    "license_validity",
    "license_modified_number",
    "license_modified_validity",
    "license_renewed_number",
    "license_renewed_validity",
    "violations",
]

csv_rows = []
for p in providers:
    m = p["main"]   # main row (columns 0-14)

    # ── Provider identity (always from main row) ──────────────────────────
    provider_name = clean(m[1])
    eik           = clean(m[2])
    municipality  = clean(m[3])
    settlement    = clean(m[4])
    address       = clean(m[5])

    # ── Aggregate services from all rows (unique, order-preserved) ────────
    seen_services = set()
    services = []
    for row in [m] + p["extra"]:
        svc = clean(row[6])
        if svc and svc not in seen_services:
            seen_services.add(svc)
            services.append(svc)
    social_service = "; ".join(services)

    # ── Target group: take from first row that has one ────────────────────
    target_group = clean(m[7])
    if not target_group:
        for row in p["extra"]:
            target_group = clean(row[7])
            if target_group:
                break

    # ── License numbers: one per service row, semicolon-separated ─────────
    license_numbers = []
    for row in [m] + p["extra"]:
        ln = clean(row[8])
        if ln:
            license_numbers.append(ln)
    license_number = "; ".join(license_numbers)

    # License validity from main row (shared across services)
    license_validity = fmt_date(m[9])

    # Modified license (main row only)
    license_modified_number   = clean(m[10])
    license_modified_validity = fmt_date(m[11])

    # Renewed license (main row only)
    license_renewed_number   = clean(m[12])
    license_renewed_validity = fmt_date(m[13])

    # ── Violations: concatenate from all rows that have them ──────────────
    viol_parts = []
    for row in [m] + p["extra"]:
        v = clean(row[14])
        if v:
            viol_parts.append(v)
    violations = " | ".join(viol_parts)

    csv_rows.append({
        "provider_name":            provider_name,
        "eik":                      eik,
        "municipality":             municipality,
        "settlement":               settlement,
        "address":                  address,
        "social_service":           social_service,
        "target_group":             target_group,
        "license_number":           license_number,
        "license_validity":         license_validity,
        "license_modified_number":  license_modified_number,
        "license_modified_validity":license_modified_validity,
        "license_renewed_number":   license_renewed_number,
        "license_renewed_validity": license_renewed_validity,
        "violations":               violations,
    })

# ── Write CSV ─────────────────────────────────────────────────────────────────
with open(OUT_CSV, "w", encoding="utf-8-sig", newline="") as fh:
    writer = csv.DictWriter(fh, fieldnames=CSV_COLUMNS)
    writer.writeheader()
    writer.writerows(csv_rows)

print(f"Written {len(csv_rows)} providers -> {OUT_CSV}")
