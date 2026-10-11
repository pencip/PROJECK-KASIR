import React, { useRef, useState } from 'react';
import { 
  UploadCloud, 
  Zap, 
  ShieldCheck, 
  FileUp, 
  Sparkles, 
  CheckCircle2, 
  ArrowRight,
  Settings,
  Download,
  FolderOpen,
  Cloud
} from 'lucide-react';
import { ActiveTab } from '../types';

interface HeroHomeProps {
  onSelectFiles: (files: FileList | File[]) => void;
  setActiveTab: (tab: ActiveTab) => void;
}

export const HeroHome: React.FC<HeroHomeProps> = ({ onSelectFiles, setActiveTab }) => {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [isDragging, setIsDragging] = useState(false);

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      onSelectFiles(e.dataTransfer.files);
    }
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      onSelectFiles(e.target.files);
    }
  };

  const openFileDialog = () => {
    fileInputRef.current?.click();
  };

  return (
    <div className="w-full max-w-7xl mx-auto px-3 sm:px-6 py-4 sm:py-6">
      {/* Hidden file input */}
      <input
        ref={fileInputRef}
        type="file"
        multiple
        accept=".pdf,application/pdf"
        className="hidden"
        onChange={handleFileChange}
      />

      {/* Main Glassmorphism Frame */}
      <div className="relative rounded-[28px] sm:rounded-[36px] bg-[#141226]/90 border border-purple-500/20 shadow-2xl p-6 sm:p-10 lg:p-12 overflow-hidden backdrop-blur-xl">
        {/* Soft Background Radial Glow */}
        <div className="absolute -top-32 -left-32 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl pointer-events-none" />
        <div className="absolute -bottom-32 -right-32 w-96 h-96 bg-pink-600/15 rounded-full blur-3xl pointer-events-none" />

        <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
          {/* Left Column: Headline & Call to Action */}
          <div className="lg:col-span-7 flex flex-col items-start space-y-6">
            {/* Top Pill Badge */}
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1e1b3a] border border-purple-500/30 text-purple-300 text-xs font-semibold tracking-wide">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <span>100% Cepat, Gratis & Privasi Aman</span>
            </div>

            {/* Main Headline */}
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.12]">
              Kelola Cerdas.{' '}
              <span className="block mt-1 font-serif italic font-medium bg-gradient-to-r from-pink-300 via-rose-300 to-red-400 bg-clip-text text-transparent">
                Kerja Lebih Cepat.
              </span>
            </h1>

            {/* Subtitle Description */}
            <p className="text-purple-200/80 text-sm sm:text-base leading-relaxed max-w-xl">
              Ucapkan selamat tinggal pada tugas berulang. Platform PDF kami memudahkan penggabungan,
              kompresi, dan konversi dokumen sehingga tim Anda bisa fokus pada hal yang penting.
            </p>

            {/* Primary Action Button */}
            <div className="pt-2 flex flex-wrap items-center gap-4">
              <button
                onClick={openFileDialog}
                className="group inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-pink-600 via-rose-500 to-red-500 hover:from-pink-500 hover:to-rose-600 text-white font-bold text-sm sm:text-base shadow-xl shadow-pink-600/30 hover:shadow-pink-600/50 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
              >
                <FileUp className="w-5 h-5 transition-transform group-hover:-translate-y-0.5" />
                <span>Pilih Dokumen PDF</span>
              </button>

              <button
                onClick={() => setActiveTab('merge')}
                className="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-[#1d1a38] hover:bg-[#252247] border border-purple-500/20 text-purple-200 text-xs sm:text-sm font-semibold transition-all"
              >
                <span>Mulai Menggabung (Merge)</span>
                <ArrowRight className="w-4 h-4 text-pink-400" />
              </button>
            </div>

            {/* Social Proof & Security Badges */}
            <div className="pt-4 flex flex-wrap items-center gap-5 sm:gap-8 text-xs text-purple-200/80">
              {/* Avatars & Rating */}
              <div className="flex items-center gap-2.5">
                <div className="flex -space-x-1.5 overflow-hidden">
                  <div className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-500 text-white text-[10px] font-bold ring-2 ring-[#141226]">A</div>
                  <div className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-500 text-white text-[10px] font-bold ring-2 ring-[#141226]">R</div>
                  <div className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-bold ring-2 ring-[#141226]">D</div>
                  <div className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-[10px] font-bold ring-2 ring-[#141226]">+</div>
                </div>
                <div className="flex items-center gap-1">
                  <span className="text-amber-400 font-bold tracking-tight">★★★★★</span>
                  <span className="font-semibold text-white ml-0.5">4.9/5</span>
                  <span className="text-purple-300/70">(Kepuasan Pengguna)</span>
                </div>
              </div>

              {/* Security Shield */}
              <div className="flex items-center gap-1.5 text-purple-300">
                <ShieldCheck className="w-4 h-4 text-emerald-400" />
                <span className="font-medium">File Terenkripsi Otomatis</span>
              </div>
            </div>
          </div>

          {/* Right Column: Interactive Drop Zone */}
          <div className="lg:col-span-5 flex flex-col space-y-4">
            <div
              onDragOver={handleDragOver}
              onDragLeave={handleDragLeave}
              onDrop={handleDrop}
              onClick={openFileDialog}
              className={`group relative rounded-2xl sm:rounded-3xl border-2 border-dashed transition-all duration-300 p-8 sm:p-10 flex flex-col items-center justify-center text-center cursor-pointer ${
                isDragging
                  ? 'border-pink-500 bg-pink-500/10 scale-[1.02]'
                  : 'border-purple-500/30 hover:border-pink-500/60 bg-[#191632]/80 hover:bg-[#1f1b3d]/90'
              }`}
            >
              {/* Cloud Upload Icon */}
              <div className="w-16 h-16 rounded-2xl bg-purple-900/40 border border-purple-500/30 flex items-center justify-center mb-4 text-purple-300 group-hover:text-pink-400 group-hover:scale-110 transition-transform shadow-lg shadow-purple-900/40">
                <UploadCloud className="w-8 h-8" />
              </div>

              <h2 className="text-base sm:text-lg font-bold text-white tracking-tight mb-1">
                Tarik & Lepaskan File PDF
              </h2>
              <p className="text-xs text-purple-300/70 mb-4">
                atau klik untuk memilih file dari perangkat Anda
              </p>

              {/* Pills */}
              <div className="flex items-center gap-2 mb-4">
                <span className="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#231f45] text-purple-300 border border-purple-500/20">
                  PDF
                </span>
                <span className="px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#231f45] text-purple-300/80 border border-purple-500/20">
                  Maks 50MB
                </span>
              </div>

              {/* Cloud Storage Support */}
              <div className="flex items-center gap-3 text-[11px] text-purple-300/70 pt-2 border-t border-purple-500/15 w-full justify-center">
                <span>Dukungan:</span>
                <div className="flex items-center gap-1.5 font-medium text-purple-200">
                  <Cloud className="w-3.5 h-3.5 text-blue-400" />
                  <span>Google Drive</span>
                </div>
                <div className="flex items-center gap-1.5 font-medium text-purple-200">
                  <FolderOpen className="w-3.5 h-3.5 text-indigo-400" />
                  <span>Dropbox</span>
                </div>
              </div>
            </div>

            {/* Bottom 2 Feature Badges */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              {/* Instant Compression */}
              <div className="flex items-center gap-3 p-3.5 rounded-xl bg-[#1a1735]/70 border border-purple-500/20">
                <div className="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                  <Zap className="w-4 h-4 fill-emerald-400/30" />
                </div>
                <div>
                  <h3 className="text-xs font-bold text-white">Kompresi Instan</h3>
                  <p className="text-[11px] text-purple-300/70">Hemat ukuran hingga 80%</p>
                </div>
              </div>

              {/* Privacy Guaranteed */}
              <div className="flex items-center gap-3 p-3.5 rounded-xl bg-[#1a1735]/70 border border-purple-500/20">
                <div className="w-8 h-8 rounded-lg bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-xs font-bold text-white">Privasi Terjamin</h3>
                  <p className="text-[11px] text-purple-300/70">Dihapus otomatis dalam 2 jam</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* 3-Step Workflow Section */}
        <div className="mt-12 pt-8 border-t border-purple-500/15">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
            {/* Step 1 */}
            <div className="flex flex-col items-center text-center p-3">
              <div className="w-10 h-10 rounded-xl bg-[#211d42] border border-purple-500/30 flex items-center justify-center text-purple-300 mb-2">
                <FileUp className="w-5 h-5 text-pink-400" />
              </div>
              <h4 className="text-xs font-bold text-white mb-0.5">1. Upload</h4>
              <p className="text-[11px] text-purple-300/70">Pilih atau tarik file PDF Anda</p>
            </div>

            {/* Step 2 */}
            <div className="flex flex-col items-center text-center p-3">
              <div className="w-10 h-10 rounded-xl bg-[#211d42] border border-purple-500/30 flex items-center justify-center text-purple-300 mb-2">
                <Settings className="w-5 h-5 text-indigo-400" />
              </div>
              <h4 className="text-xs font-bold text-white mb-0.5">2. Pilih Alat</h4>
              <p className="text-[11px] text-purple-300/70">Gabung, kompres, atau konversi</p>
            </div>

            {/* Step 3 */}
            <div className="flex flex-col items-center text-center p-3">
              <div className="w-10 h-10 rounded-xl bg-[#211d42] border border-purple-500/30 flex items-center justify-center text-purple-300 mb-2">
                <Download className="w-5 h-5 text-emerald-400" />
              </div>
              <h4 className="text-xs font-bold text-white mb-0.5">3. Download</h4>
              <p className="text-[11px] text-purple-300/70">Hasil siap diunduh dalam hitungan detik</p>
            </div>
          </div>
        </div>

        {/* Bottom Footer Attribution */}
        <div className="mt-8 pt-4 border-t border-purple-500/10 text-center">
          <p className="text-[11px] text-purple-400/60 font-medium tracking-wide">
            © 2026 <span className="text-white font-bold">I-LIKE</span> <span className="text-pink-400 font-bold">PDF</span> — Dibuat untuk Kemudahan Dokumen Tim.
          </p>
        </div>
      </div>
    </div>
  );
};
