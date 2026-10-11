import React, { useState, useRef } from 'react';
import { 
  FileText, 
  UploadCloud, 
  ArrowUp, 
  ArrowDown, 
  Trash2, 
  Download, 
  Sparkles, 
  CheckCircle2, 
  RefreshCw,
  Plus,
  Layers
} from 'lucide-react';
import { mergePdfFiles } from '../../services/pdfService';
import { saveFileHistoryItem, formatBytes } from '../../services/storageService';
import { ProcessedFile, User } from '../../types';

interface MergePdfToolProps {
  currentUser: User | null;
  initialFiles?: File[];
  onFinish?: () => void;
}

export const MergePdfTool: React.FC<MergePdfToolProps> = ({ currentUser, initialFiles = [] }) => {
  const [files, setFiles] = useState<File[]>(initialFiles);
  const [isProcessing, setIsProcessing] = useState(false);
  const [result, setResult] = useState<{ blob: Blob; url: string; fileName: string; size: number; pages: number } | null>(null);
  const [error, setError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleAddFiles = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      const newFiles = Array.from(e.target.files).filter((f) => f.type === 'application/pdf' || f.name.endsWith('.pdf'));
      setFiles((prev) => [...prev, ...newFiles]);
      setError(null);
    }
  };

  const moveUp = (index: number) => {
    if (index === 0) return;
    const copy = [...files];
    const [moved] = copy.splice(index, 1);
    copy.splice(index - 1, 0, moved);
    setFiles(copy);
  };

  const moveDown = (index: number) => {
    if (index === files.length - 1) return;
    const copy = [...files];
    const [moved] = copy.splice(index, 1);
    copy.splice(index + 1, 0, moved);
    setFiles(copy);
  };

  const removeFile = (index: number) => {
    setFiles((prev) => prev.filter((_, i) => i !== index));
  };

  const handleMerge = async () => {
    if (files.length < 2) {
      setError('Harap tambahkan minimal 2 file PDF untuk digabungkan.');
      return;
    }

    try {
      setIsProcessing(true);
      setError(null);

      const totalOriginalSize = files.reduce((acc, f) => acc + f.size, 0);
      const outputFileName = `Merged_${files[0].name.replace(/\.pdf$/i, '')}_gabungan.pdf`;

      const { blob, pageCount } = await mergePdfFiles(files);
      const url = URL.createObjectURL(blob);

      setResult({
        blob,
        url,
        fileName: outputFileName,
        size: blob.size,
        pages: pageCount
      });

      // Save to history
      const historyItem: ProcessedFile = {
        id: 'hist_' + Date.now(),
        userId: currentUser?.id,
        fileName: outputFileName,
        tool: 'merge',
        originalSize: totalOriginalSize,
        processedSize: blob.size,
        createdAt: new Date().toISOString(),
        mimeType: 'application/pdf',
        pageCount: pageCount
      };
      saveFileHistoryItem(historyItem);
    } catch (err: any) {
      console.error(err);
      setError(err?.message || 'Gagal menggabungkan PDF. Pastikan file tidak terkunci sandi.');
    } finally {
      setIsProcessing(false);
    }
  };

  const resetTool = () => {
    setFiles([]);
    setResult(null);
    setError(null);
  };

  return (
    <div className="w-full max-w-5xl mx-auto px-4 py-6">
      <div className="rounded-3xl bg-[#141226]/90 border border-purple-500/20 p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
        {/* Tool Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-purple-500/15">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
              <Layers className="w-6 h-6" />
            </div>
            <div>
              <h2 className="text-xl sm:text-2xl font-bold text-white tracking-tight">Merge PDF</h2>
              <p className="text-xs sm:text-sm text-purple-300/70">
                Gabungkan beberapa dokumen PDF menjadi satu urutan yang rapi.
              </p>
            </div>
          </div>

          {files.length > 0 && !result && (
            <button
              onClick={() => fileInputRef.current?.click()}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600/20 border border-purple-500/30 text-purple-200 text-xs font-semibold hover:bg-purple-600/30 transition-colors"
            >
              <Plus className="w-4 h-4 text-pink-400" />
              <span>Tambah File Lagi</span>
            </button>
          )}
        </div>

        <input
          ref={fileInputRef}
          type="file"
          multiple
          accept=".pdf,application/pdf"
          className="hidden"
          onChange={handleAddFiles}
        />

        {/* Error message */}
        {error && (
          <div className="mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
            <span>⚠️ {error}</span>
          </div>
        )}

        {/* State 1: File selection when empty */}
        {files.length === 0 && !result && (
          <div
            onClick={() => fileInputRef.current?.click()}
            className="mt-8 border-2 border-dashed border-purple-500/30 hover:border-pink-500/60 rounded-3xl p-10 sm:p-16 flex flex-col items-center justify-center text-center cursor-pointer bg-[#181530]/50 hover:bg-[#1f1b3d]/70 transition-all"
          >
            <div className="w-16 h-16 rounded-2xl bg-purple-900/40 border border-purple-500/30 flex items-center justify-center text-purple-300 mb-4 shadow-lg">
              <UploadCloud className="w-8 h-8 text-pink-400" />
            </div>
            <h3 className="text-lg font-bold text-white mb-1">Pilih File PDF untuk Digabungkan</h3>
            <p className="text-xs text-purple-300/70 mb-4 max-w-sm">
              Klik atau seret beberapa file PDF ke area ini. Anda bisa mengatur urutan halaman sesuka hati.
            </p>
            <button
              type="button"
              className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 text-white font-semibold text-xs shadow-lg shadow-pink-600/30"
            >
              Pilih Dokumen PDF
            </button>
          </div>
        )}

        {/* State 2: List of files to merge with reorder controls */}
        {files.length > 0 && !result && (
          <div className="mt-6 space-y-4">
            <div className="flex items-center justify-between text-xs text-purple-300/80 px-1">
              <span>Urutan Dokumen ({files.length} file dipilih)</span>
              <span>Total: {formatBytes(files.reduce((a, b) => a + b.size, 0))}</span>
            </div>

            <div className="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
              {files.map((file, idx) => (
                <div
                  key={`${file.name}-${idx}`}
                  className="flex items-center justify-between p-3.5 rounded-2xl bg-[#191632] border border-purple-500/20 hover:border-purple-400/40 transition-colors"
                >
                  <div className="flex items-center gap-3 overflow-hidden">
                    <span className="w-6 h-6 rounded-lg bg-purple-900/50 text-purple-300 text-xs font-bold flex items-center justify-center shrink-0">
                      {idx + 1}
                    </span>
                    <FileText className="w-5 h-5 text-pink-400 shrink-0" />
                    <div className="truncate">
                      <p className="text-xs sm:text-sm font-semibold text-white truncate">{file.name}</p>
                      <p className="text-[11px] text-purple-300/60">{formatBytes(file.size)}</p>
                    </div>
                  </div>

                  {/* Actions: Move Up, Move Down, Delete */}
                  <div className="flex items-center gap-1 shrink-0 pl-2">
                    <button
                      onClick={() => moveUp(idx)}
                      disabled={idx === 0}
                      className="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-purple-300 disabled:opacity-30 disabled:cursor-not-allowed"
                      title="Pindahkan ke atas"
                    >
                      <ArrowUp className="w-3.5 h-3.5" />
                    </button>
                    <button
                      onClick={() => moveDown(idx)}
                      disabled={idx === files.length - 1}
                      className="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-purple-300 disabled:opacity-30 disabled:cursor-not-allowed"
                      title="Pindahkan ke bawah"
                    >
                      <ArrowDown className="w-3.5 h-3.5" />
                    </button>
                    <button
                      onClick={() => removeFile(idx)}
                      className="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300"
                      title="Hapus file"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>

            {/* Merge Action CTA */}
            <div className="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-purple-500/15">
              <button
                onClick={resetTool}
                className="text-xs text-purple-300/60 hover:text-purple-200 transition-colors"
              >
                Batalkan & Hapus Pilihan
              </button>

              <button
                onClick={handleMerge}
                disabled={isProcessing || files.length < 2}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-pink-600 via-rose-500 to-red-500 hover:from-pink-500 hover:to-rose-600 text-white font-bold text-sm shadow-xl shadow-pink-600/30 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <RefreshCw className="w-4 h-4 animate-spin" />
                    <span>Sedang Menggabungkan...</span>
                  </>
                ) : (
                  <>
                    <Sparkles className="w-4 h-4" />
                    <span>Gabungkan {files.length} PDF Sekarang</span>
                  </>
                )}
              </button>
            </div>
          </div>
        )}

        {/* State 3: Completed Result */}
        {result && (
          <div className="mt-8 text-center flex flex-col items-center p-6 sm:p-10 rounded-3xl bg-[#191632] border border-purple-500/30">
            <div className="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 mb-4 shadow-lg shadow-emerald-500/20">
              <CheckCircle2 className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-bold text-white mb-1">Penggabungan PDF Berhasil!</h3>
            <p className="text-xs sm:text-sm text-purple-300/80 mb-6 max-w-md">
              Semua dokumen PDF Anda telah sukses disatukan menjadi 1 file lengkap ({result.pages} halaman total).
            </p>

            <div className="p-4 rounded-2xl bg-[#131124] border border-purple-500/20 w-full max-w-md text-left mb-6">
              <p className="text-xs font-semibold text-white truncate mb-1">{result.fileName}</p>
              <div className="flex items-center justify-between text-[11px] text-purple-300/70">
                <span>Ukuran File: {formatBytes(result.size)}</span>
                <span>Jumlah: {result.pages} Halaman</span>
              </div>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-3">
              <a
                href={result.url}
                download={result.fileName}
                className="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-500 hover:to-rose-600 text-white font-bold text-xs sm:text-sm shadow-xl shadow-pink-600/30 hover:scale-105 transition-all"
              >
                <Download className="w-4 h-4" />
                <span>Unduh File Gabungan</span>
              </a>

              <button
                onClick={resetTool}
                className="px-5 py-3 rounded-2xl bg-[#211d42] border border-purple-500/30 text-purple-200 text-xs sm:text-sm font-semibold hover:bg-[#282450] transition-colors"
              >
                Gabung Dokumen Lain
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
