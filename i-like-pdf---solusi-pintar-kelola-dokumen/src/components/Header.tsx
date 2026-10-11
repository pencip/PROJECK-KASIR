import React from 'react';
import { ActiveTab, User } from '../types';
import { 
  FileText, 
  Sun, 
  Moon, 
  HelpCircle, 
  User as UserIcon, 
  LogOut, 
  Database, 
  FolderArchive,
  Menu,
  X
} from 'lucide-react';

interface HeaderProps {
  activeTab: ActiveTab;
  setActiveTab: (tab: ActiveTab) => void;
  currentUser: User | null;
  onOpenAuth: () => void;
  onLogout: () => void;
  onOpenHelp: () => void;
  onOpenLaravelModal: () => void;
  darkMode: boolean;
  setDarkMode: (val: boolean) => void;
}

export const Header: React.FC<HeaderProps> = ({
  activeTab,
  setActiveTab,
  currentUser,
  onOpenAuth,
  onLogout,
  onOpenHelp,
  onOpenLaravelModal,
  darkMode,
  setDarkMode
}) => {
  const [mobileMenuOpen, setMobileMenuOpen] = React.useState(false);

  const tabs: { id: ActiveTab; label: string }[] = [
    { id: 'home', label: 'Beranda' },
    { id: 'merge', label: 'Merge PDF' },
    { id: 'compress', label: 'Compress PDF' },
    { id: 'pdf-to-word', label: 'PDF to Word' },
    { id: 'ocr', label: 'OCR Dokumen' },
    { id: 'files', label: 'Manajemen File' }
  ];

  return (
    <header className="relative z-30 w-full pt-4 px-4 sm:px-6">
      <div className="max-w-7xl mx-auto flex items-center justify-between py-3">
        {/* Brand Logo */}
        <button 
          onClick={() => setActiveTab('home')}
          className="flex items-center gap-3 text-left group transition-transform hover:scale-[1.02]"
        >
          <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-pink-600 via-rose-500 to-red-500 flex items-center justify-center shadow-lg shadow-pink-500/25">
            <span className="font-extrabold text-white text-xl tracking-tighter">D</span>
          </div>
          <div>
            <div className="flex items-center gap-1">
              <span className="font-extrabold text-xl tracking-tight text-white">I-LIKE</span>
              <span className="font-extrabold text-xl tracking-tight bg-gradient-to-r from-pink-500 to-rose-400 bg-clip-text text-transparent">PDF</span>
            </div>
            <p className="text-[11px] font-medium text-purple-300/70 -mt-0.5 tracking-wide">PDF Tools Suite</p>
          </div>
        </button>

        {/* Center Pill Nav (Desktop) */}
        <nav className="hidden lg:flex items-center p-1.5 rounded-full bg-[#1b1933]/90 border border-purple-500/20 shadow-inner backdrop-blur-md">
          {tabs.map((tab) => {
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`px-4 py-2 text-xs font-semibold rounded-full transition-all duration-200 cursor-pointer ${
                  isActive
                    ? 'bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 text-white shadow-md shadow-purple-600/30'
                    : 'text-purple-200/70 hover:text-white hover:bg-white/5'
                }`}
              >
                {tab.label}
              </button>
            );
          })}
        </nav>

        {/* Right Action Controls */}
        <div className="hidden sm:flex items-center gap-2.5">
          {/* Laravel & MySQL Arch Button */}
          <button
            onClick={onOpenLaravelModal}
            className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-pink-300 bg-pink-500/10 border border-pink-500/20 hover:bg-pink-500/20 transition-colors"
            title="Lihat Arsitektur Laravel Breeze + MySQL"
          >
            <Database className="w-3.5 h-3.5 text-pink-400" />
            <span className="hidden md:inline">Laravel + MySQL</span>
          </button>

          {/* Help Button */}
          <button
            onClick={onOpenHelp}
            className="flex items-center gap-1 text-xs text-purple-200/80 hover:text-white px-2 py-1.5 rounded-md hover:bg-white/5 transition-colors"
          >
            <HelpCircle className="w-4 h-4" />
            <span>Bantuan</span>
          </button>

          {/* Dark / Light Toggle */}
          <button
            onClick={() => setDarkMode(!darkMode)}
            className="p-1.5 rounded-full bg-[#1c1935] border border-purple-500/20 hover:border-purple-400/40 text-amber-300 transition-colors"
            title={darkMode ? 'Mode Gelap Aktif' : 'Mode Terang Aktif'}
          >
            {darkMode ? <Sun className="w-4 h-4 fill-amber-400" /> : <Moon className="w-4 h-4 text-slate-300" />}
          </button>

          {/* User Auth Info */}
          {currentUser ? (
            <div className="flex items-center gap-2 pl-1">
              <button
                onClick={() => setActiveTab('files')}
                className="flex items-center gap-2 py-1 px-2.5 rounded-full bg-[#1b1933] border border-purple-500/25 hover:border-purple-400/50 transition-colors"
              >
                <img
                  src={currentUser.avatar || `https://api.dicebear.com/7.x/initials/svg?seed=${currentUser.name}`}
                  alt={currentUser.name}
                  className="w-6 h-6 rounded-full object-cover ring-1 ring-pink-500/50"
                />
                <span className="text-xs font-semibold text-purple-100 max-w-[100px] truncate">{currentUser.name}</span>
              </button>
              <button
                onClick={onLogout}
                className="p-1.5 text-purple-300/60 hover:text-rose-400 rounded-md hover:bg-rose-500/10 transition-colors"
                title="Keluar (Logout)"
              >
                <LogOut className="w-4 h-4" />
              </button>
            </div>
          ) : (
            <button
              onClick={onOpenAuth}
              className="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-sm shadow-purple-600/30 transition-all"
            >
              <UserIcon className="w-3.5 h-3.5" />
              <span>Masuk (Breeze)</span>
            </button>
          )}
        </div>

        {/* Mobile menu trigger */}
        <div className="flex items-center gap-2 lg:hidden">
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="p-2 rounded-lg bg-[#1c1935] border border-purple-500/20 text-purple-200"
          >
            {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>
        </div>
      </div>

      {/* Mobile Menu Dropdown */}
      {mobileMenuOpen && (
        <div className="lg:hidden mt-2 p-4 rounded-2xl bg-[#141226] border border-purple-500/30 shadow-2xl backdrop-blur-xl animate-in fade-in slide-in-from-top-2">
          <div className="flex flex-col gap-1.5">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => {
                  setActiveTab(tab.id);
                  setMobileMenuOpen(false);
                }}
                className={`w-full text-left px-3 py-2 text-sm font-semibold rounded-lg ${
                  activeTab === tab.id
                    ? 'bg-purple-600 text-white'
                    : 'text-purple-200 hover:bg-white/5'
                }`}
              >
                {tab.label}
              </button>
            ))}

            <div className="h-px bg-purple-500/20 my-2" />

            <div className="flex items-center justify-between pt-1">
              <button
                onClick={() => {
                  onOpenLaravelModal();
                  setMobileMenuOpen(false);
                }}
                className="flex items-center gap-1.5 text-xs text-pink-300 font-semibold"
              >
                <Database className="w-4 h-4" />
                <span>Laravel 11 + Breeze Code</span>
              </button>

              <button
                onClick={() => setDarkMode(!darkMode)}
                className="p-1.5 rounded-lg bg-purple-900/30 text-amber-300"
              >
                {darkMode ? <Sun className="w-4 h-4 fill-amber-400" /> : <Moon className="w-4 h-4" />}
              </button>
            </div>

            {currentUser ? (
              <div className="flex items-center justify-between pt-2">
                <span className="text-xs text-purple-300 truncate">Halo, {currentUser.name}</span>
                <button
                  onClick={() => {
                    onLogout();
                    setMobileMenuOpen(false);
                  }}
                  className="text-xs text-rose-400 flex items-center gap-1"
                >
                  <LogOut className="w-3.5 h-3.5" /> Keluar
                </button>
              </div>
            ) : (
              <button
                onClick={() => {
                  onOpenAuth();
                  setMobileMenuOpen(false);
                }}
                className="w-full mt-2 py-2 rounded-lg bg-purple-600 text-white font-semibold text-xs flex items-center justify-center gap-2"
              >
                <UserIcon className="w-4 h-4" /> Masuk Akun
              </button>
            )}
          </div>
        </div>
      )}
    </header>
  );
};
