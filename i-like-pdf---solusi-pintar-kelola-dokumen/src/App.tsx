import React, { useState, useEffect } from 'react';
import { ActiveTab, User } from './types';
import { getCurrentUser, logoutUser } from './services/authService';
import { Header } from './components/Header';
import { HeroHome } from './components/HeroHome';
import { MergePdfTool } from './components/tools/MergePdfTool';
import { CompressPdfTool } from './components/tools/CompressPdfTool';
import { PdfToWordTool } from './components/tools/PdfToWordTool';
import { OcrPdfTool } from './components/tools/OcrPdfTool';
import { FileManager } from './components/FileManager';
import { AuthModal } from './components/AuthModal';
import { LaravelGuideModal } from './components/LaravelGuideModal';
import { HelpModal } from './components/HelpModal';
import { 
  Layers, 
  Zap, 
  FileSpreadsheet, 
  ScanText, 
  X,
  FileText,
  Sparkles
} from 'lucide-react';
import { formatBytes } from './services/storageService';

export default function App() {
  const [activeTab, setActiveTab] = useState<ActiveTab>('home');
  const [currentUser, setCurrentUser] = useState<User | null>(null);
  const [darkMode, setDarkMode] = useState<boolean>(true);
  const [isAuthOpen, setIsAuthOpen] = useState<boolean>(false);
  const [isLaravelGuideOpen, setIsLaravelGuideOpen] = useState<boolean>(false);
  const [isHelpOpen, setIsHelpOpen] = useState<boolean>(false);

  // When files are picked on Hero, modal asks what action to take
  const [stagedFiles, setStagedFiles] = useState<File[]>([]);
  const [showActionChooser, setShowActionChooser] = useState<boolean>(false);

  useEffect(() => {
    // Load current user
    const user = getCurrentUser();
    setCurrentUser(user);
  }, []);

  const handleLogout = () => {
    logoutUser();
    setCurrentUser(null);
  };

  const handleHeroFilesSelected = (fileList: FileList | File[]) => {
    const arr = Array.from(fileList);
    if (arr.length === 0) return;

    setStagedFiles(arr);
    setShowActionChooser(true);
  };

  const chooseToolAction = (tab: ActiveTab) => {
    setActiveTab(tab);
    setShowActionChooser(false);
  };

  return (
    <div className={`min-h-screen flex flex-col font-sans transition-colors duration-200 ${darkMode ? 'bg-[#0b0c16] text-slate-100' : 'bg-slate-50 text-slate-900'}`}>
      {/* Top Ambient Glow Effects */}
      <div className="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[350px] bg-purple-600/10 blur-[130px] pointer-events-none rounded-full" />
      <div className="fixed top-32 right-10 w-[400px] h-[300px] bg-pink-600/10 blur-[120px] pointer-events-none rounded-full" />

      {/* Navigation Header */}
      <Header
        activeTab={activeTab}
        setActiveTab={setActiveTab}
        currentUser={currentUser}
        onOpenAuth={() => setIsAuthOpen(true)}
        onLogout={handleLogout}
        onOpenHelp={() => setIsHelpOpen(true)}
        onOpenLaravelModal={() => setIsLaravelGuideOpen(true)}
        darkMode={darkMode}
        setDarkMode={setDarkMode}
      />

      {/* Main View Area */}
      <main className="flex-1 w-full py-2 sm:py-4">
        {activeTab === 'home' && (
          <HeroHome
            onSelectFiles={handleHeroFilesSelected}
            setActiveTab={setActiveTab}
          />
        )}

        {activeTab === 'merge' && (
          <MergePdfTool
            currentUser={currentUser}
            initialFiles={stagedFiles}
          />
        )}

        {activeTab === 'compress' && (
          <CompressPdfTool
            currentUser={currentUser}
            initialFile={stagedFiles[0]}
          />
        )}

        {activeTab === 'pdf-to-word' && (
          <PdfToWordTool
            currentUser={currentUser}
            initialFile={stagedFiles[0]}
          />
        )}

        {activeTab === 'ocr' && (
          <OcrPdfTool
            currentUser={currentUser}
            initialFile={stagedFiles[0]}
          />
        )}

        {activeTab === 'files' && (
          <FileManager
            currentUser={currentUser}
            setActiveTab={setActiveTab}
            onOpenLaravelModal={() => setIsLaravelGuideOpen(true)}
          />
        )}
      </main>

      {/* Quick Action Chooser Modal when files are dragged into Hero */}
      {showActionChooser && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in">
          <div className="relative w-full max-w-lg rounded-3xl bg-[#151329] border border-purple-500/30 p-6 sm:p-8 shadow-2xl text-left">
            <button
              onClick={() => setShowActionChooser(false)}
              className="absolute top-5 right-5 p-1.5 rounded-full bg-white/5 hover:bg-white/10 text-purple-300 hover:text-white"
            >
              <X className="w-4 h-4" />
            </button>

            <div className="flex items-center gap-3 mb-2">
              <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-pink-600 to-rose-500 flex items-center justify-center text-white">
                <Sparkles className="w-5 h-5" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-white">
                  {stagedFiles.length} File PDF Dipilih
                </h3>
                <p className="text-xs text-purple-300/70">
                  {stagedFiles[0]?.name} {stagedFiles.length > 1 ? `(+${stagedFiles.length - 1} lainnya)` : ''} ({formatBytes(stagedFiles.reduce((a, b) => a + b.size, 0))})
                </p>
              </div>
            </div>

            <p className="text-xs text-purple-200/80 mb-5">
              Pilih tindakan yang ingin Anda lakukan terhadap dokumen ini:
            </p>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
              <button
                onClick={() => chooseToolAction('merge')}
                className="flex items-start gap-3 p-3.5 rounded-2xl bg-[#1d193b] border border-purple-500/20 hover:border-purple-400 hover:bg-[#25204c] text-left transition-all group"
              >
                <div className="w-8 h-8 rounded-lg bg-purple-600/20 text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                  <Layers className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-xs font-bold text-white">Merge PDF</h4>
                  <p className="text-[11px] text-purple-300/70">Gabungkan file jadi satu</p>
                </div>
              </button>

              <button
                onClick={() => chooseToolAction('compress')}
                className="flex items-start gap-3 p-3.5 rounded-2xl bg-[#1d193b] border border-purple-500/20 hover:border-emerald-400 hover:bg-[#25204c] text-left transition-all group"
              >
                <div className="w-8 h-8 rounded-lg bg-emerald-600/20 text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                  <Zap className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-xs font-bold text-white">Compress PDF</h4>
                  <p className="text-[11px] text-purple-300/70">Kecilkan ukuran file</p>
                </div>
              </button>

              <button
                onClick={() => chooseToolAction('pdf-to-word')}
                className="flex items-start gap-3 p-3.5 rounded-2xl bg-[#1d193b] border border-purple-500/20 hover:border-blue-400 hover:bg-[#25204c] text-left transition-all group"
              >
                <div className="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                  <FileSpreadsheet className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-xs font-bold text-white">PDF to Word</h4>
                  <p className="text-[11px] text-purple-300/70">Ubah jadi dokumen .docx</p>
                </div>
              </button>

              <button
                onClick={() => chooseToolAction('ocr')}
                className="flex items-start gap-3 p-3.5 rounded-2xl bg-[#1d193b] border border-purple-500/20 hover:border-amber-400 hover:bg-[#25204c] text-left transition-all group"
              >
                <div className="w-8 h-8 rounded-lg bg-amber-600/20 text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                  <ScanText className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-xs font-bold text-white">OCR Dokumen</h4>
                  <p className="text-[11px] text-purple-300/70">Ekstrak teks pindaian</p>
                </div>
              </button>
            </div>

            <div className="flex justify-end pt-2">
              <button
                onClick={() => setShowActionChooser(false)}
                className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-purple-300"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Global Modals */}
      <AuthModal
        isOpen={isAuthOpen}
        onClose={() => setIsAuthOpen(false)}
        onSuccess={(user) => {
          setCurrentUser(user);
          setIsAuthOpen(false);
        }}
      />

      <LaravelGuideModal
        isOpen={isLaravelGuideOpen}
        onClose={() => setIsLaravelGuideOpen(false)}
      />

      <HelpModal
        isOpen={isHelpOpen}
        onClose={() => setIsHelpOpen(false)}
      />
    </div>
  );
}
