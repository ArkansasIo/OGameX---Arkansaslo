# 8-bit Fixed-Record Database

A self-contained reference implementation for **20-byte, little-endian fixed-width binary records** shared by SQLite, Excel, and Z80 assembly.

## Record contract

| Offset | Size | Field | Encoding |
|---:|---:|---|---|
| 0 | 1 | magic | `0x52` (`R`) |
| 1 | 1 | version | `1` |
| 2 | 1 | flags | bit 0 active, bit 1 deleted, bits 2–7 reserved |
| 3 | 1 | record_type | `1` player, `2` planet, `3` resource, `4` event |
| 4 | 4 | record_id | unsigned 32-bit little-endian |
| 8 | 4 | value | unsigned 32-bit little-endian |
| 12 | 2 | quantity | unsigned 16-bit little-endian |
| 14 | 2 | owner_id | unsigned 16-bit little-endian |
| 16 | 4 | timestamp | unsigned Unix seconds, little-endian |

The record is exactly 20 bytes. Text labels belong in the SQL/Excel views and are not stored in the binary record. The canonical checksum is **CRC-16/CCITT-FALSE** over bytes 0–19 (polynomial `0x1021`, initial value `0xFFFF`, no reflection or final XOR).

## Included artifacts

- `sql/schema.sql`: SQLite schema, constraints, views, import/export helpers, and seed data.
- `data/records.bin`: sample 20-byte records generated from the seed data.
- `config/records.json`: machine-readable format and validation configuration.
- `excel_template.xlsx`: spreadsheet template with a `Records` sheet, validation lists, formulas, and an `Instructions` sheet.
- `asm/records_z80.asm`: Z80 routines for little-endian encoding/decoding, CRC, and record validation.
- `tools/record_tool.py`: dependency-light CLI for generating, inspecting, validating, and exporting records.
- `tests/test_records.py`: cross-format tests for binary, JSON, SQL, and XLSX consistency.

## Quick start

```sh
cd fixed-records
python3 tools/record_tool.py generate --output data/records.bin
python3 tools/record_tool.py inspect data/records.bin
python3 tools/record_tool.py validate data/records.bin
python3 tests/test_records.py
sqlite3 data/records.db < sql/schema.sql
```

The spreadsheet is generated with:

```sh
python3 tools/build_excel.py
```

The Z80 source is intentionally assembler-dialect conservative: it uses standard Z80 instructions and labels, with no undocumented opcodes. `RecordBuffer` is the 20-byte destination/source buffer; `RecordId`, `RecordValue`, `RecordQuantity`, `RecordOwner`, and `RecordTimestamp` are little-endian working variables.

## Invariants

All numeric values are unsigned and range-checked before packing. `record_id` and `timestamp` are 32-bit; `quantity` and `owner_id` are 16-bit. A binary file must have a byte length divisible by 20, every record must have the magic/version pair, and every reserved flag bit must be zero. SQLite stores the same fields as integers and uses `CHECK` constraints to enforce the range.

## License

This module follows the repository's existing license.
