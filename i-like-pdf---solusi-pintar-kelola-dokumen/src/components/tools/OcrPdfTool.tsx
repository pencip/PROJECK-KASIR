import React, { useState, useRef } from 'react';
import { 
  ScanText, 
  UploadCloud, 
  Download, 
  Copy, 
  Check, 
  RefreshCw, 
  FileText, 
  Languages, 
  Sparkles,
  CheckCircle2
} from 'lucide-react';
import { performOcr } from '../../services/pdfService';
import { saveFileHistoryItem, formatBytes } from '../../services/storageService';
import { ProcessedFile, User } from '../../types';

interface OcrPdfToolProps {
  currentUser: User | null;
  initialFile?: File;
}

export const OcrPdfTool: React.FC<OcrPdfToolProps> = ({ currentUser, initialFile }) => {
  const [file, setFile] = useState<File | null>(initialFile || null);
  const [language, setLanguage] = useState<'ind+eng' | 'ind' | 'eng'>('ind+eng');
  const [isProcessing, setIsProcessing] = useState(false);
  const [progressStatus, setProgressStatus] = useState<string>('');
  const [progressPercent, setProgressPercent] = useState<number>(0);
  const [copied, setCopied] = useState(false);
  const [result, setResult] = useState<{
    text: string;
    confidence: number;
    wordsCount: number;
    fileName: string;
  } | null>(null);
  const [error, setError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      setFile(e.target.files[0]);
      setResult(null);
      setError(null);
    }
  };

  const handleRunOcr = async () => {
    if (!file) return;

    try {
      setIsProcessing(true);
      setError(null);
      setProgressPercent(10);
      setProgressStatus('Memulai mesin OCR...');

      const ocrResult = await performOcr(file, language, (pct, status) => {
        setProgressPercent(pct);
        setProgressStatus(status);
      });

      const outputFileName = `${file.name.replace(/\.[^/.]+$/, '')}_OCR.txt`;
      setResult({
        text: ocrResult.text,
        confidence: ocrResult.confidence,
        wordsCount: ocrResult.wordsCount,
        fileName: outputFileName
      });

      // Save to history
      const historyItem: ProcessedFile = {
        id: 'hist_' + Date.now(),
        userId: currentUser?.id,
        fileName: outputFileName,
        tool: 'ocr',
        originalSize: file.size,
        processedSize: ocrResult.text.length,
        createdAt: new Date().toISOString(),
        mimeType: 'text/plain',
        extractedText: ocrResult.text
      };
      saveFileHistoryItem(historyItem);
    } catch (err: any) {
      console.error(err);
      setError('Gagal memproses OCR. Pastikan dokumen memiliki teks yang jelas.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleCopy = () => {
    if (!result?.text) return;
    navigator.clipboard.writeText(result.text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleDownloadTxt = () => {
    if (!result?.text) return;
    const blob = new Blob([result.text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = result.fileName;
    a.click();
    URL.revokeObjectURL(url);
  };

  const resetTool = () => {
    setFile(null);
    setResult(null);
    setError(null);
    setProgressPercent(0);
    setProgressStatus('');
  };

  return (
    <div className="w-full max-w-5xl mx-auto px-4 py-6">
      <div className="rounded-3xl bg-[#141226]/90 border border-purple-500/20 p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
        {/* Header */}
        <div className="flex items-center gap-3 pb-6 border-b border-purple-500/15">
          <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
            <ScanText className="w-6 h-6" />
          </div>
          <div>
            <h2 className="text-xl sm:text-2xl font-bold text-white tracking-tight">OCR Dokumen (Pindai Teks)</h2>
            <p className="text-xs sm:text-sm text-purple-300/70">
              Ekstrak teks dari PDF pindaian, gambar struk, atau dokumen fisik secara otomatis.
            </p>
          </div>
        </div>

        <input
          ref={fileInputRef}
          type="file"
          accept=".pdf,image/png,image/jpeg,image/webp,application/pdf"
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
            <div className="w-16 h-16 rounded-2xl bg-amber-900/40 border border-amber-500/30 flex items-center justify-center text-amber-300 mb-4 shadow-lg">
              <UploadCloud className="w-8 h-8 text-amber-400" />
            </div>
            <h3 className="text-lg font-bold text-white mb-1">Pilih Dokumen PDF atau Gambar untuk OCR</h3>
            <p className="text-xs text-purple-300/70 mb-4 max-w-sm">
              Mendukung PDF hasil scan, invoice, kuitansi, atau gambar foto dokumen (PNG/JPG).
            </p>
            <button
              type="button"
              className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-rose-500 to-pink-500 text-white font-semibold text-xs shadow-lg shadow-amber-500/30"
            >
              Pilih Dokumen Scan
            </button>
          </div>
        )}

        {/* State 2: Ready */}
        {file && !result && (
          <div className="mt-6 space-y-6">
            <div className="flex items-center justify-between p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
              <div className="flex items-center gap-3 overflow-hidden">
                <FileText className="w-6 h-6 text-pink-400 shrink-0" />
                <div className="truncate">
                  <p className="text-sm font-semibold text-white truncate">{file.name}</p>
                  <p className="text-xs text-purple-300/60">Ukuran file: {formatBytes(file.size)}</p>
                </div>
              </div>
              <button
                onClick={() => fileInputRef.current?.click()}
                className="text-xs text-purple-300 hover:text-white underline ml-3 shrink-0"
              >
                Ganti File
              </button>
            </div>

            {/* Language Selector */}
            <div>
              <label className="block text-xs font-semibold text-purple-200 mb-2">
                Pilih Bahasa Pengenalan Karakter:
              </label>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <button
                  type="button"
                  onClick={() => setLanguage('ind+eng')}
                  className={`p-3 rounded-xl border text-left text-xs font-semibold transition-all ${
                    language === 'ind+eng'
                      ? 'bg-purple-900/40 border-pink-500 text-white'
                      : 'bg-[#181530] border-purple-500/20 text-purple-300'
                  }`}
                >
                  🇮🇩 + 🇬🇧 Bahasa Indonesia & Inggris (Direkomendasikan)
                </button>
                <button
                  type="button"
                  onClick={() => setLanguage('ind')}
                  className={`p-3 rounded-xl border text-left text-xs font-semibold transition-all ${
                    language === 'ind'
                      ? 'bg-purple-900/40 border-pink-500 text-white'
                      : 'bg-[#181530] border-purple-500/20 text-purple-300'
                  }`}
                >
                  🇮🇩 Bahasa Indonesia Khusus
                </button>
                <button
                  type="button"
                  onClick={() => setLanguage('eng')}
                  className={`p-3 rounded-xl border text-left text-xs font-semibold transition-all ${
                    language === 'eng'
                      ? 'bg-purple-900/40 border-pink-500 text-white'
                      : 'bg-[#181530] border-purple-500/20 text-purple-300'
                  }`}
                >
                  🇬🇧 English Document
                </button>
              </div>
            </div>

            {/* Processing Bar if running */}
            {isProcessing && (
              <div className="p-4 rounded-2xl bg-[#191632] border border-purple-500/30 space-y-2">
                <div className="flex items-center justify-between text-xs text-purple-200">
                  <span>{progressStatus || 'Sedang memindai karakter...'}</span>
                  <span className="font-bold text-pink-400">{progressPercent}%</span>
                </div>
                <div className="w-full h-2 rounded-full bg-[#110e24] overflow-hidden">
                  <div
                    className="h-full bg-gradient-to-r from-amber-500 via-rose-500 to-pink-500 transition-all duration-300"
                    style={{ width: `${progressPercent}%` }}
                  />
                </div>
              </div>
            )}

            <div className="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-purple-500/15">
              <button
                onClick={resetTool}
                disabled={isProcessing}
                className="text-xs text-purple-300/60 hover:text-purple-200"
              >
                Batal
              </button>

              <button
                onClick={handleRunOcr}
                disabled={isProcessing}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 via-rose-500 to-pink-500 hover:from-amber-400 hover:to-pink-600 text-white font-bold text-sm shadow-xl shadow-amber-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50"
              >
                {isProcessing ? (
                  <>
                    <RefreshCw className="w-4 h-4 animate-spin" />
                    <span>Memindai Teks OCR...</span>
                  </>
                ) : (
                  <>
                    <Sparkles className="w-4 h-4" />
                    <span>Mulai Pindai Teks (OCR)</span>
                  </>
                )}
              </button>
            </div>
          </div>
        )}

        {/* State 3: OCR Results */}
        {result && (
          <div className="mt-6 space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                  <CheckCircle2 className="w-5 h-5" />
                </div>
                <div>
                  <h4 className="text-xs sm:text-sm font-bold text-white">Pemindaian OCR Sukses</h4>
                  <p className="text-[11px] text-purple-300/70">
                    Akurasi: <span className="text-emerald-400 font-bold">{result.confidence}%</span> • {result.wordsCount} kata terdeteksi
                  </p>
                </div>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={handleCopy}
                  className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600/30 hover:bg-purple-600/50 text-purple-200 border border-purple-500/30 text-xs font-semibold transition-colors"
                >
                  {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copied ? 'Tersalin!' : 'Salin Teks'}</span>
                </button>

                <button
                  onClick={handleDownloadTxt}
                  className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 text-white text-xs font-semibold shadow-md shadow-pink-600/20 hover:scale-105 transition-all"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Unduh .TXT</span>
                </button>
              </div>
            </div>

            {/* Extracted Text Box */}
            <div className="relative">
              <textarea
                readOnly
                value={result.text}
                rows={12}
                className="w-full rounded-2xl bg-[#121022] border border-purple-500/20 p-4 text-xs sm:text-sm font-mono text-purple-100 focus:outline-none focus:ring-1 focus:ring-purple-400 resize-y leading-relaxed"
              />
            </div>

            <div className="flex justify-center pt-2">
              <button
                onClick={resetTool}
                className="px-6 py-2.5 rounded-xl bg-[#211d42] border border-purple-500/30 text-purple-200 text-xs font-semibold hover:bg-[#282450] transition-colors"
              >
                Pindai Dokumen Lain
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
