#!/usr/bin/env python3
import argparse, json, struct, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CFG = json.loads((ROOT/'config/records.json').read_text())
SIZE = CFG['record_size']

def crc16(data):
    crc = CFG['crc16']['initial']
    for byte in data:
        crc ^= byte << 8
        for _ in range(8):
            crc = ((crc << 1) ^ CFG['crc16']['polynomial']) & 0xffff if crc & 0x8000 else (crc << 1) & 0xffff
    return crc

def pack(r):
    vals = [r['flags'], r['record_type'], r['record_id'], r['value'], r['quantity'], r['owner_id'], r['timestamp']]
    if not (0 <= r['flags'] <= 3 and 1 <= r['record_type'] <= 4): raise ValueError('flags/type out of range')
    if not all(0 <= x <= m for x,m in zip(vals[2:], (0xffffffff,0xffffffff,0xffff,0xffff,0xffffffff))): raise ValueError('numeric field out of range')
    return struct.pack('<BBBBIIHHI', CFG['magic'], CFG['version'], *vals)

def unpack(b):
    if len(b) != SIZE: raise ValueError('record must be exactly 20 bytes')
    magic, version, flags, typ, rid, value, qty, owner, ts = struct.unpack('<BBBBIIHHI', b)
    return {'magic':magic,'version':version,'flags':flags,'record_type':typ,'record_id':rid,'value':value,'quantity':qty,'owner_id':owner,'timestamp':ts}

def validate(data):
    if len(data) % SIZE: raise ValueError('file length is not divisible by 20')
    out=[]
    for i in range(0,len(data),SIZE):
        r=unpack(data[i:i+SIZE])
        if r['magic'] != CFG['magic'] or r['version'] != CFG['version']: raise ValueError(f'invalid header at record {i//SIZE}')
        if r['flags'] & CFG['reserved_flag_mask']: raise ValueError(f'reserved flags at record {i//SIZE}')
        if not 1 <= r['record_type'] <= 4: raise ValueError(f'invalid type at record {i//SIZE}')
        out.append(r)
    return out

def sample():
    return [dict(flags=1,record_type=1,record_id=1001,value=2500,quantity=1,owner_id=7,timestamp=1726550400),dict(flags=1,record_type=2,record_id=2001,value=90000,quantity=12,owner_id=7,timestamp=1726550400),dict(flags=1,record_type=3,record_id=3001,value=450,quantity=320,owner_id=7,timestamp=1726550400)]

def main():
    p=argparse.ArgumentParser(); sub=p.add_subparsers(dest='cmd',required=True)
    g=sub.add_parser('generate'); g.add_argument('--output',required=True)
    i=sub.add_parser('inspect'); i.add_argument('input')
    v=sub.add_parser('validate'); v.add_argument('input')
    args=p.parse_args()
    if args.cmd == 'generate':
        Path(args.output).write_bytes(b''.join(pack(r) for r in sample())); print(f'wrote {len(sample())} records ({SIZE*len(sample())} bytes)')
    else:
        records=validate(Path(args.input).read_bytes())
        if args.cmd == 'validate': print(f'valid: {len(records)} records')
        else:
            for n,r in enumerate(records): print(n, json.dumps(r), f'crc16=0x{crc16(pack(r)):04X}')
if __name__ == '__main__': main()
