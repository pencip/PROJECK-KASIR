import { ProcessedFile } from '../types';

const STORAGE_KEY = 'ilike_pdf_history_v1';

export function getFileHistory(): ProcessedFile[] {
  try {
    const data = localStorage.getItem(STORAGE_KEY);
    if (!data) {
      // Provide clean default sample files to showcase the dashboard immediately
      const initialSeed: ProcessedFile[] = [
        {
          id: 'hist-1',
          fileName: 'Laporan_Keuangan_Q3_Merged.pdf',
          tool: 'merge',
          originalSize: 4820000,
          processedSize: 4650000,
          reductionPercentage: 4,
          createdAt: new Date(Date.now() - 3600000 * 2).toISOString(),
          mimeType: 'application/pdf',
          pageCount: 14
        },
        {
          id: 'hist-2',
          fileName: 'Proposal_Proyek_2026_Compressed.pdf',
          tool: 'compress',
          originalSize: 8400000,
          processedSize: 1850000,
          reductionPercentage: 78,
          createdAt: new Date(Date.now() - 3600000 * 5).toISOString(),
          mimeType: 'application/pdf',
          pageCount: 22
        },
        {
          id: 'hist-3',
          fileName: 'Kontrak_Kerjasama_Vendor.docx',
          tool: 'pdf-to-word',
          originalSize: 1250000,
          processedSize: 320000,
          createdAt: new Date(Date.now() - 3600000 * 24).toISOString(),
          mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
          pageCount: 6
        },
        {
          id: 'hist-4',
          fileName: 'Scan_Kuitansi_Pembayaran.txt',
          tool: 'ocr',
          originalSize: 2100000,
          processedSize: 18000,
          createdAt: new Date(Date.now() - 3600000 * 48).toISOString(),
          mimeType: 'text/plain',
          extractedText: 'No Invoice: INV/2026/09/881\nTotal Pembayaran: Rp 14.500.000\nStatus: Lunas\nMetode: Transfer Bank BCA'
        }
      ];
      localStorage.setItem(STORAGE_KEY, JSON.stringify(initialSeed));
      return initialSeed;
    }
    return JSON.parse(data);
  } catch (e) {
    console.error('Failed to load history:', e);
    return [];
  }
}

export function saveFileHistoryItem(item: ProcessedFile): void {
  try {
    const list = getFileHistory();
    // Prepend new item
    const updated = [item, ...list];
    localStorage.setItem(STORAGE_KEY, JSON.stringify(updated));
  } catch (e) {
    console.error('Failed to save history item:', e);
  }
}

export function deleteFileHistoryItem(id: string): void {
  try {
    const list = getFileHistory();
    const updated = list.filter((item) => item.id !== id);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(updated));
  } catch (e) {
    console.error('Failed to delete history item:', e);
  }
}

export function clearAllFileHistory(): void {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (e) {
    console.error('Failed to clear history:', e);
  }
}

export function formatBytes(bytes: number, decimals: number = 2): string {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
