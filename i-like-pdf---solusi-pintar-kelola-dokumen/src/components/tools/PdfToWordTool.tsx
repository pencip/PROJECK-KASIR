import React, { useState, useRef } from 'react';
import { 
  FileCode, 
  UploadCloud, 
  Download, 
  CheckCircle2, 
  RefreshCw, 
  FileText,
  FileSpreadsheet,
  FileCheck,
  Sparkles
} from 'lucide-react';
import { convertPdfToWord } from '../../services/pdfService';
import { saveFileHistoryItem, formatBytes } from '../../services/storageService';
import { ProcessedFile, User } from '../../types';

interface PdfToWordToolProps {
  currentUser: User | null;
  initialFile?: File;
}

export const PdfToWordTool: React.FC<PdfToWordToolProps> = ({ currentUser, initialFile }) => {
  const [file, setFile] = useState<File | null>(initialFile || null);
  const [isConverting, setIsConverting] = useState(false);
  const [result, setResult] = useState<{
    blob: Blob;
    url: string;
    fileName: string;
    originalSize: number;
    docSize: number;
    textLength: number;
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

  const handleConvert = async () => {
    if (!file) return;

    try {
      setIsConverting(true);
      setError(null);

      const res = await convertPdfToWord(file);
      const url = URL.createObjectURL(res.blob);

      setResult({
        blob: res.blob,
        url,
        fileName: res.fileName,
        originalSize: file.size,
        docSize: res.blob.size,
        textLength: res.textCount
      });

      // Save to history
      const historyItem: ProcessedFile = {
        id: 'hist_' + Date.now(),
        userId: currentUser?.id,
        fileName: res.fileName,
        tool: 'pdf-to-word',
        originalSize: file.size,
        processedSize: res.blob.size,
        createdAt: new Date().toISOString(),
        mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      };
      saveFileHistoryItem(historyItem);
    } catch (err: any) {
      console.error(err);
      setError('Gagal mengonversi PDF ke Word. Silakan coba dokumen lain.');
    } finally {
      setIsConverting(false);
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
          <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
            <FileSpreadsheet className="w-6 h-6" />
          </div>
          <div>
            <h2 className="text-xl sm:text-2xl font-bold text-white tracking-tight">PDF to Word (.docx)</h2>
            <p className="text-xs sm:text-sm text-purple-300/70">
              Ubah file PDF menjadi dokumen Word DOCX yang dapat diedit langsung dengan mudah.
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
            <div className="w-16 h-16 rounded-2xl bg-blue-900/40 border border-blue-500/30 flex items-center justify-center text-blue-300 mb-4 shadow-lg">
              <UploadCloud className="w-8 h-8 text-blue-400" />
            </div>
            <h3 className="text-lg font-bold text-white mb-1">Pilih File PDF untuk Dikonversi ke Word</h3>
            <p className="text-xs text-purple-300/70 mb-4 max-w-sm">
              Teks dan paragraf akan diekstraksi ke file Word (.docx) yang dapat diedit secara penuh.
            </p>
            <button
              type="button"
              className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-xs shadow-lg shadow-blue-600/30"
            >
              Pilih Dokumen PDF
            </button>
          </div>
        )}

        {/* State 2: Ready to convert */}
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

            <div className="p-4 rounded-2xl bg-[#191632]/60 border border-purple-500/20 text-xs text-purple-200/80 space-y-2">
              <div className="flex items-center gap-2 text-blue-400 font-semibold">
                <FileCheck className="w-4 h-4" />
                <span>Format Output: Dokumen Microsoft Word Standar (.docx)</span>
              </div>
              <p className="text-[11px] text-purple-300/70">
                Kompatibel dengan Microsoft Word 2013-2024, Office 365, Google Docs, WPS Office, dan LibreOffice.
              </p>
            </div>

            <div className="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-purple-500/15">
              <button
                onClick={resetTool}
                className="text-xs text-purple-300/60 hover:text-purple-200"
              >
                Batal
              </button>

              <button
                onClick={handleConvert}
                disabled={isConverting}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm shadow-xl shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
              >
                {isConverting ? (
                  <>
                    <RefreshCw className="w-4 h-4 animate-spin" />
                    <span>Mengonversi ke Dokumen Word...</span>
                  </>
                ) : (
                  <>
                    <Sparkles className="w-4 h-4" />
                    <span>Konversi ke Word Sekarang</span>
                  </>
                )}
              </button>
            </div>
          </div>
        )}

        {/* State 3: Result */}
        {result && (
          <div className="mt-8 text-center flex flex-col items-center p-6 sm:p-10 rounded-3xl bg-[#191632] border border-purple-500/30">
            <div className="w-16 h-16 rounded-2xl bg-blue-500/20 border border-blue-500/40 flex items-center justify-center text-blue-400 mb-4 shadow-lg shadow-blue-500/20">
              <CheckCircle2 className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-bold text-white mb-1">Konversi Word Berhasil!</h3>
            <p className="text-xs sm:text-sm text-purple-300/80 mb-6">
              File PDF Anda siap dibuka dan diedit di Microsoft Word atau Google Docs.
            </p>

            <div className="p-4 rounded-2xl bg-[#131124] border border-purple-500/20 w-full max-w-md text-left mb-6">
              <div className="flex items-center gap-2 mb-2">
                <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                  DOCX
                </span>
                <p className="text-xs font-semibold text-white truncate">{result.fileName}</p>
              </div>
              <div className="flex items-center justify-between text-[11px] text-purple-300/70">
                <span>Ukuran Dokumen: {formatBytes(result.docSize)}</span>
                <span>Karakter Teks: {result.textLength.toLocaleString()}</span>
              </div>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-3">
              <a
                href={result.url}
                download={result.fileName}
                className="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-xl shadow-blue-600/30 hover:scale-105 transition-all"
              >
                <Download className="w-4 h-4" />
                <span>Unduh File Word (.docx)</span>
              </a>

              <button
                onClick={resetTool}
                className="px-5 py-3 rounded-2xl bg-[#211d42] border border-purple-500/30 text-purple-200 text-xs sm:text-sm font-semibold hover:bg-[#282450] transition-colors"
              >
                Konversi Dokumen Lain
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
