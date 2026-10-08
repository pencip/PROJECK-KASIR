import re

with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'r') as f:
    content = f.read()

# 1. Replace 2nd and 3rd donut cards with the Quick Review / Action Card
# Also, update the first donut card to be stacked vertically and larger.
pattern_donuts = r"(<div class=\"donuts-col\">\n\n\s*@php.*?)\{\{-- ② Kategori Menu Terlaris --\}\}.*?(?=\n\s*</div>\{\{-- /donuts-col --\}\})"

def repl_donuts(m):
    first_donut_php_and_html = m.group(1)
    
    # We want to make the donut larger.
    # Replace the shared donut geometry
    first_donut_php_and_html = first_donut_php_and_html.replace(
        "$cx = 44; $cy = 44; $r = 32;",
        "$cx = 64; $cy = 64; $r = 48;"
    )
    first_donut_php_and_html = first_donut_php_and_html.replace(
        '<svg width="88" height="88" viewBox="0 0 88 88" style="flex-shrink:0;">',
        '<svg width="128" height="128" viewBox="0 0 128 128" style="flex-shrink:0;">'
    )
    first_donut_php_and_html = first_donut_php_and_html.replace(
        '<text x="{{ $cx }}" y="{{ $cy - 5 }}" text-anchor="middle" font-size="14"',
        '<text x="{{ $cx }}" y="{{ $cy - 2 }}" text-anchor="middle" font-size="20"'
    )
    first_donut_php_and_html = first_donut_php_and_html.replace(
        '<text x="{{ $cx }}" y="{{ $cy + 8 }}" text-anchor="middle" font-size="7"',
        '<text x="{{ $cx }}" y="{{ $cy + 14 }}" text-anchor="middle" font-size="9"'
    )
    # Stack the dc-body vertically
    first_donut_php_and_html = first_donut_php_and_html.replace(
        '<div class="dc-body">',
        '<div class="dc-body" style="flex-direction:column; align-items:center; padding-top:16px;">'
    )
    # Legend grid instead of flex column so it looks like the reference
    first_donut_php_and_html = first_donut_php_and_html.replace(
        '<div class="dc-legend">',
        '<div class="dc-legend" style="display:grid; grid-template-columns:1fr 1fr; gap:12px 16px; width:100%; margin-top:8px;">'
    )
    
    action_card = """
        {{-- Quick Review / Action Card --}}
        <div class="dc-card" style="display:flex; flex-direction:column; justify-content:space-between; flex:1; margin-top:8px;">
            <div>
                <div class="dc-title" style="font-size:1rem; margin-bottom:2px;">Aksi Cepat</div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:16px;">Akses pintasan kasir & resto</div>
                
                {{-- Dummy Search Bar --}}
                <div style="background:var(--bg-surface); border:1px solid var(--border-card); border-radius:8px; padding:8px 12px; display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span style="font-size:0.75rem; color:var(--text-muted);">Pilih aksi cepat...</span>
                </div>

                {{-- Action Icons --}}
                <div style="display:flex; justify-content:space-between; margin-bottom:24px; padding:0 8px;">
                    <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Buka POS">
                        <div style="width:36px; height:36px; border-radius:50%; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; color:#f59e0b;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Menu">
                        <div style="width:36px; height:36px; border-radius:50%; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; color:#60a5fa;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Promo">
                        <div style="width:36px; height:36px; border-radius:50%; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; color:#f87171;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Laporan">
                        <div style="width:36px; height:36px; border-radius:50%; background:rgba(139,92,246,0.15); display:flex; align-items:center; justify-content:center; color:#a78bfa;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button class="tbtn" style="flex:1; justify-content:center;">Kelola</button>
                <button class="tbtn accent" style="flex:1; justify-content:center; background:#111827; border-color:#374151;">Buka Kasir →</button>
            </div>
        </div>"""
    
    return first_donut_php_and_html + action_card

content, count = re.subn(pattern_donuts, repl_donuts, content, flags=re.DOTALL)
print(f"Replaced donuts-col: {count} times")

# 2. Fix the Table Header Toolbar
# Remove the accent button and completely remove the filter row
# Find toolbar and filter row
pattern_table_hdr = r"(<div class=\"toolbar\">\n\s*<button class=\"tbtn\" id=\"filterToggleBtn\">.*?</button>\n\s*<a href=\"\{\{ route\('manager\.transactions'\) \}\}\" class=\"tbtn accent\">\n\s*Lihat Seluruh Riwayat →\n\s*</a>\n\s*</div>\n\s*</div>\n\n\s*\{\{-- Filter row --\}\}\n\s*<div class=\"filter-row\" id=\"filterRow\">\n.*?</div>)"

new_toolbar = """<div class="toolbar">
            <button class="tbtn" id="filterToggleBtn" style="pointer-events:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>
            <button class="tbtn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                Sort
            </button>
            <button class="tbtn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter
            </button>
        </div>
    </div>"""

content, count2 = re.subn(pattern_table_hdr, new_toolbar, content, flags=re.DOTALL)
print(f"Replaced table header: {count2} times")

# 3. Add height: 100% to left-col cards so they fill the height
# Wait, let's make chart-card and tbl-card fill the space perfectly.
content = content.replace('<div class="tbl-card" style="margin-bottom: 0;">', '<div class="tbl-card" style="margin-bottom: 0; flex:1;">')

with open('/opt/lampp/htdocs/pos_resto/resources/views/manager/dashboard.blade.php', 'w') as f:
    f.write(content)

