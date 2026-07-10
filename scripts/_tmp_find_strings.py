import re
from pathlib import Path

t = Path(
    r"c:\Users\marce\Local Sites\sparklean-02\app\public\wp-content\plugins\breakdance\builder\dist\js\chunk-common.d2dc0afb.js"
).read_text(encoding="utf-8", errors="ignore")

for s in ("String", "Query", "All", "image_url", "Image url", "Full", "Thumbnail"):
    for m in re.finditer(re.escape(s), t):
        ctx = t[max(0, m.start() - 60) : m.start() + len(s) + 80]
        if "function" in ctx or "prototype" in ctx:
            continue
        print("---", s, "---")
        print(ctx[:180])
        break
