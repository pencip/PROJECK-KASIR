import React, { useState, useRef } from 'react';
import { 
  Zap, 
  UploadCloud, 
  Download, 
  CheckCircle2, 
  RefreshCw, 
  FileText,
  ShieldCheck,
  BarChart3,
  Sparkles
} from 'lucide-react';
import { compressPdfFile } from '../../services/pdfService';
import { saveFileHistoryItem, formatBytes } from '../../services/storageService';
import { CompressionOption, ProcessedFile, User } from '../../types';

interface CompressPdfToolProps {
  currentUser: User | null;
  initialFile?: File;
}

export const CompressPdfTool: React.FC<CompressPdfToolProps> = ({ currentUser, initialFile }) => {
  const [file, setFile] = useState<File | null>(initialFile || null);
  const [level, setLevel] = useState<'extreme' | 'recommended' | 'light'>('recommended');
  const [isProcessing, setIsProcessing] = useState(false);
  const [result, setResult] = useState<{
    blob: Blob;
    url: string;
    originalSize: number;
    compressedSize: number;
    ratioPercent: number;
    fileName: string;
  } | null>(null);
  const [error, setError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const compressionOptions: CompressionOption[] = [
    {
      id: 'extreme',
      name: 'Kompresi Ekstrem',
      reductionLabel: 'Hemat ~80%',
      reductionRatio: 0.8,
      description: 'Ukuran file terkecil, ideal untuk lampiran email atau WhatsApp.',
      dpi: 72
    },
    {
      id: 'recommended',
      name: 'Rekomendasi',
      reductionLabel: 'Hemat ~50%',
      reductionRatio: 0.5,
      description: 'Keseimbangan terbaik antara kualitas visual dan ukuran ringkas.',
      dpi: 150
    },
    {
      id: 'light',
      name: 'Kompresi Ringan',
      reductionLabel: 'Hemat ~20%',
      reductionRatio: 0.2,
      description: 'Kualitas tertinggi untuk pencetakan dokumen profesional.',
      dpi: 300
    }
  ];

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      setFile(e.target.files[0]);
      setResult(null);
      setError(null);
    }
  };

  const handleCompress = async () => {
    if (!file) return;

    try {
      setIsProcessing(true);
      setError(null);

      const res = await compressPdfFile(file, level);
      const outputFileName = `${file.name.replace(/\.pdf$/i, '')}_compressed.pdf`;
      const url = URL.createObjectURL(res.blob);

      setResult({
        blob: res.blob,
        url,
        originalSize: res.originalSize,
        compressedSize: res.compressedSize,
        ratioPercent: res.ratioPercent,
        fileName: outputFileName
      });

      // Save to history
      const historyItem: ProcessedFile = {
        id: 'hist_' + Date.now(),
        userId: currentUser?.id,
        fileName: outputFileName,
        tool: 'compress',
        originalSize: res.originalSize,
        processedSize: res.compressedSize,
        reductionPercentage: res.ratioPercent,
        createdAt: new Date().toISOString(),
        mimeType: 'application/pdf'
      };
      saveFileHistoryItem(historyItem);
    } catch (err: any) {
      console.error(err);
      setError('Gagal mengompresi PDF. Silakan coba lagi.');
    } finally {
      setIsProcessing(false);
    }
  };

  const resetTool = () => {
    setFile(null);
    setResult(null);
    setError(null);
  };

  return (
    <div className="w-full max-w-5xl mx-auto px-4 py-6">
      <div className="rounded-3xl bg-[#141226]/90 border border-purple-500/20 p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
        {/* Header */}
        <div className="flex items-center gap-3 pb-6 border-b border-purple-500/15">
          <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-600/30">
            <Zap className="w-6 h-6 fill-white/20" />
          </div>
          <div>
            <h2 className="text-xl sm:text-2xl font-bold text-white tracking-tight">Compress PDF</h2>
            <p className="text-xs sm:text-sm text-purple-300/70">
              Kecilkan ukuran file PDF Anda tanpa kehilangan keterbacaan dokumen.
            </p>
          </div>
        </div>

        <input
          ref={fileInputRef}
          type="file"
          accept=".pdf,application/pdf"
          className="hidden"
          onChange={handleFileChange}
        />

        {error && (
          <div className="mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
            ⚠️ {error}
          </div>
        )}

        {/* State 1: Upload */}
        {!file && !result && (
          <div
            onClick={() => fileInputRef.current?.click()}
            className="mt-8 border-2 border-dashed border-purple-500/30 hover:border-pink-500/60 rounded-3xl p-10 sm:p-16 flex flex-col items-center justify-center text-center cursor-pointer bg-[#181530]/50 hover:bg-[#1f1b3d]/70 transition-all"
          >
            <div className="w-16 h-16 rounded-2xl bg-emerald-900/40 border border-emerald-500/30 flex items-center justify-center text-emerald-300 mb-4 shadow-lg">
              <UploadCloud className="w-8 h-8" />
            </div>
            <h3 className="text-lg font-bold text-white mb-1">Pilih File PDF untuk Dikompresi</h3>
            <p className="text-xs text-purple-300/70 mb-4 max-w-sm">
              Kecilkan ukuran PDF hingga 80% dengan cepat, aman, dan instan.
            </p>
            <button
              type="button"
              className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30"
            >
              Pilih Dokumen PDF
            </button>
          </div>
        )}

        {/* State 2: Compression options */}
        {file && !result && (
          <div className="mt-6 space-y-6">
            {/* Selected File Card */}
            <div className="flex items-center justify-between p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
              <div className="flex items-center gap-3 overflow-hidden">
                <FileText className="w-6 h-6 text-pink-400 shrink-0" />
                <div className="truncate">
                  <p className="text-sm font-semibold text-white truncate">{file.name}</p>
                  <p className="text-xs text-purple-300/60">Ukuran awal: {formatBytes(file.size)}</p>
                </div>
              </div>
              <button
                onClick={() => fileInputRef.current?.click()}
                className="text-xs text-purple-300 hover:text-white underline ml-3 shrink-0"
              >
                Ganti File
              </button>
            </div>

            {/* Quality Options */}
            <div>
              <label className="block text-xs font-semibold text-purple-200 mb-3">
                Pilih Tingkat Kompresi:
              </label>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                {compressionOptions.map((opt) => {
                  const isSelected = level === opt.id;
                  return (
                    <div
                      key={opt.id}
                      onClick={() => setLevel(opt.id)}
                      className={`p-4 rounded-2xl border transition-all cursor-pointer ${
                        isSelected
                          ? 'bg-purple-900/30 border-pink-500 ring-1 ring-pink-500/40 shadow-lg'
                          : 'bg-[#181530] border-purple-500/20 hover:border-purple-400/40'
                      }`}
                    >
                      <div className="flex items-center justify-between mb-2">
                        <span className="text-xs font-bold text-white">{opt.name}</span>
                        <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                          {opt.reductionLabel}
                        </span>
                      </div>
                      <p className="text-[11px] text-purple-300/70 leading-relaxed">{opt.description}</p>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Compress CTA */}
            <div className="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-purple-500/15">
              <button
                onClick={resetTool}
                className="text-xs text-purple-300/60 hover:text-purple-200"
              >
                Batal
              </button>

              <button
                onClick={handleCompress}
                disabled={isProcessing}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600 hover:from-emerald-500 hover:to-teal-600 text-white font-bold text-sm shadow-xl shadow-emerald-600/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <RefreshCw className="w-4 h-4 animate-spin" />
                    <span>Sedang Mengompresi Dokumen...</span>
                  </>
                ) : (
                  <>
                    <Zap className="w-4 h-4 fill-white" />
                    <span>Kompresi PDF Sekarang</span>
                  </>
                )}
              </button>
            </div>
          </div>
        )}

        {/* State 3: Compression Result */}
        {result && (
          <div className="mt-8 text-center flex flex-col items-center p-6 sm:p-10 rounded-3xl bg-[#191632] border border-purple-500/30">
            <div className="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 mb-4 shadow-lg shadow-emerald-500/20">
              <CheckCircle2 className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-bold text-white mb-1">Kompresi Selesai!</h3>
            <p className="text-xs sm:text-sm text-purple-300/80 mb-6">
              File Anda berhasil diperkecil dengan penghematan <span className="text-emerald-400 font-bold">{result.ratioPercent}%</span>.
            </p>

            {/* Comparison Metrics */}
            <div className="grid grid-cols-2 gap-4 w-full max-w-md p-4 rounded-2xl bg-[#131124] border border-purple-500/20 mb-6">
              <div className="text-left border-r border-purple-500/20 pr-3">
                <span className="text-[10px] text-purple-300/60 block">Ukuran Awal</span>
                <span className="text-sm font-bold text-slate-300 line-through">
                  {formatBytes(result.originalSize)}
                </span>
              </div>
              <div className="text-left pl-3">
                <span className="text-[10px] text-emerald-400 block font-semibold">Ukuran Baru (Hemat {result.ratioPercent}%)</span>
                <span className="text-sm font-extrabold text-emerald-300">
                  {formatBytes(result.compressedSize)}
                </span>
              </div>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-3">
              <a
                href={result.url}
                download={result.fileName}
                className="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-white font-bold text-xs sm:text-sm shadow-xl shadow-emerald-600/30 hover:scale-105 transition-all"
              >
                <Download className="w-4 h-4" />
                <span>Unduh File Terkompresi</span>
              </a>

              <button
                onClick={resetTool}
                className="px-5 py-3 rounded-2xl bg-[#211d42] border border-purple-500/30 text-purple-200 text-xs sm:text-sm font-semibold hover:bg-[#282450] transition-colors"
              >
                Kompres Dokumen Lain
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
