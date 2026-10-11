import React, { useState } from 'react';
import { 
  X, 
  Lock, 
  Mail, 
  User as UserIcon, 
  ShieldCheck, 
  CheckCircle2, 
  ArrowRight,
  Sparkles,
  KeyRound
} from 'lucide-react';
import { loginUser, registerUser } from '../services/authService';
import { User } from '../types';

interface AuthModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: (user: User) => void;
}

export const AuthModal: React.FC<AuthModalProps> = ({ isOpen, onClose, onSuccess }) => {
  const [mode, setMode] = useState<'login' | 'register'>('login');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    setTimeout(() => {
      if (mode === 'login') {
        const res = loginUser(email, password);
        if (res.success && res.user) {
          onSuccess(res.user);
          onClose();
        } else {
          setError(res.error || 'Login gagal.');
        }
      } else {
        if (!name.trim()) {
          setError('Nama lengkap wajib diisi.');
          setLoading(false);
          return;
        }
        const res = registerUser(name, email, password);
        if (res.success && res.user) {
          onSuccess(res.user);
          onClose();
        } else {
          setError(res.error || 'Pendaftaran gagal.');
        }
      }
      setLoading(false);
    }, 400);
  };

  const handleQuickDemo = (demoEmail: string) => {
    setEmail(demoEmail);
    setPassword('password');
    const res = loginUser(demoEmail, 'password');
    if (res.success && res.user) {
      onSuccess(res.user);
      onClose();
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md animate-in fade-in">
      <div className="relative w-full max-w-md rounded-3xl bg-[#151329] border border-purple-500/30 p-6 sm:p-8 shadow-2xl text-left">
        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-5 right-5 p-1.5 rounded-full bg-white/5 hover:bg-white/10 text-purple-300 hover:text-white transition-colors"
        >
          <X className="w-4 h-4" />
        </button>

        {/* Laravel Breeze Badge & Brand */}
        <div className="flex items-center gap-3 mb-4">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-pink-600 to-rose-600 flex items-center justify-center text-white shadow-md shadow-pink-600/30 font-bold text-sm">
            LB
          </div>
          <div>
            <div className="flex items-center gap-1.5">
              <span className="text-xs font-bold uppercase tracking-wider text-pink-400">Laravel Breeze</span>
              <span className="text-[10px] px-1.5 py-0.2 rounded bg-purple-900/60 text-purple-300 border border-purple-500/30">
                MySQL Auth
              </span>
            </div>
            <h3 className="text-lg font-bold text-white tracking-tight">
              {mode === 'login' ? 'Masuk ke Akun Anda' : 'Daftar Akun Baru'}
            </h3>
          </div>
        </div>

        <p className="text-xs text-purple-300/70 mb-5">
          Simpan riwayat file terkompresi, hasil penggabungan, dan OCR Anda di cloud secara terenkripsi.
        </p>

        {error && (
          <div className="mb-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
            <span>⚠️ {error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-3.5">
          {mode === 'register' && (
            <div>
              <label className="block text-[11px] font-semibold text-purple-200 mb-1">
                Nama Lengkap
              </label>
              <div className="relative">
                <UserIcon className="w-4 h-4 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  required
                  placeholder="Budi Santoso"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full pl-9 pr-3 py-2.5 rounded-xl bg-[#1d1938] border border-purple-500/30 text-xs text-white placeholder-purple-400/40 focus:outline-none focus:border-pink-500"
                />
              </div>
            </div>
          )}

          <div>
            <label className="block text-[11px] font-semibold text-purple-200 mb-1">
              Alamat Email
            </label>
            <div className="relative">
              <Mail className="w-4 h-4 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="email"
                required
                placeholder="nama@email.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full pl-9 pr-3 py-2.5 rounded-xl bg-[#1d1938] border border-purple-500/30 text-xs text-white placeholder-purple-400/40 focus:outline-none focus:border-pink-500"
              />
            </div>
          </div>

          <div>
            <div className="flex items-center justify-between mb-1">
              <label className="text-[11px] font-semibold text-purple-200">
                Kata Sandi
              </label>
              {mode === 'login' && (
                <span className="text-[10px] text-pink-400 hover:underline cursor-pointer">
                  Lupa sandi?
                </span>
              )}
            </div>
            <div className="relative">
              <Lock className="w-4 h-4 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="password"
                required
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full pl-9 pr-3 py-2.5 rounded-xl bg-[#1d1938] border border-purple-500/30 text-xs text-white placeholder-purple-400/40 focus:outline-none focus:border-pink-500"
              />
            </div>
          </div>

          <div className="flex items-center justify-between text-xs pt-1">
            <label className="flex items-center gap-2 cursor-pointer text-purple-300">
              <input
                type="checkbox"
                checked={remember}
                onChange={(e) => setRemember(e.target.checked)}
                className="rounded accent-pink-500"
              />
              <span className="text-[11px]">Ingat saya (Laravel Session)</span>
            </label>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 rounded-xl bg-gradient-to-r from-pink-600 via-rose-500 to-red-500 hover:from-pink-500 hover:to-rose-600 text-white font-bold text-xs sm:text-sm shadow-xl shadow-pink-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <span>{mode === 'login' ? 'Masuk (Laravel Breeze)' : 'Daftar Akun'}</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </form>

        {/* Mode Toggle */}
        <div className="mt-4 pt-3 border-t border-purple-500/15 text-center text-xs text-purple-300/80">
          {mode === 'login' ? (
            <p>
              Belum punya akun?{' '}
              <button
                type="button"
                onClick={() => {
                  setMode('register');
                  setError(null);
                }}
                className="font-bold text-pink-400 hover:underline ml-1"
              >
                Daftar sekarang
              </button>
            </p>
          ) : (
            <p>
              Sudah memiliki akun?{' '}
              <button
                type="button"
                onClick={() => {
                  setMode('login');
                  setError(null);
                }}
                className="font-bold text-pink-400 hover:underline ml-1"
              >
                Masuk di sini
              </button>
            </p>
          )}
        </div>

        {/* 1-Click Quick Demo Login */}
        <div className="mt-5 p-3 rounded-2xl bg-[#191632] border border-purple-500/20">
          <div className="flex items-center gap-1.5 text-[11px] font-semibold text-purple-300 mb-2">
            <KeyRound className="w-3.5 h-3.5 text-amber-400" />
            <span>Akses Cepat Akun Demo (Laravel Seed):</span>
          </div>
          <div className="grid grid-cols-2 gap-2">
            <button
              type="button"
              onClick={() => handleQuickDemo('budi@ilike-pdf.id')}
              className="px-2.5 py-1.5 rounded-lg bg-[#231f45] hover:bg-[#2c2755] text-[11px] font-medium text-purple-200 border border-purple-500/20 text-left truncate transition-colors"
            >
              👤 Budi (User)
            </button>
            <button
              type="button"
              onClick={() => handleQuickDemo('rina@ilike-pdf.id')}
              className="px-2.5 py-1.5 rounded-lg bg-[#231f45] hover:bg-[#2c2755] text-[11px] font-medium text-purple-200 border border-purple-500/20 text-left truncate transition-colors"
            >
              ⭐ Rina (Admin)
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
