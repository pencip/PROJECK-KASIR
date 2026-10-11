import React, { useState, useEffect } from 'react';
import { 
  FolderArchive, 
  Search, 
  Download, 
  Trash2, 
  FileText, 
  Layers, 
  Zap, 
  FileSpreadsheet, 
  ScanText, 
  Database,
  Calendar,
  HardDrive,
  CheckCircle2,
  Share2,
  ExternalLink,
  Plus
} from 'lucide-react';
import { 
  getFileHistory, 
  deleteFileHistoryItem, 
  clearAllFileHistory, 
  formatBytes 
} from '../services/storageService';
import { ProcessedFile, User, ActiveTab } from '../types';

interface FileManagerProps {
  currentUser: User | null;
  setActiveTab: (tab: ActiveTab) => void;
  onOpenLaravelModal: () => void;
}

export const FileManager: React.FC<FileManagerProps> = ({ 
  currentUser, 
  setActiveTab, 
  onOpenLaravelModal 
}) => {
  const [files, setFiles] = useState<ProcessedFile[]>([]);
  const [search, setSearch] = useState('');
  const [toolFilter, setToolFilter] = useState<'all' | 'merge' | 'compress' | 'pdf-to-word' | 'ocr'>('all');
  const [selectedFileForModal, setSelectedFileForModal] = useState<ProcessedFile | null>(null);
  const [notification, setNotification] = useState<string | null>(null);

  const loadFiles = () => {
    setFiles(getFileHistory());
  };

  useEffect(() => {
    loadFiles();
  }, []);

  const handleDelete = (id: string, e: React.MouseEvent) => {
    e.stopPropagation();
    deleteFileHistoryItem(id);
    loadFiles();
    showNotify('File dihapus dari riwayat.');
  };

  const handleClearAll = () => {
    if (window.confirm('Hapus seluruh riwayat pemrosesan dokumen?')) {
      clearAllFileHistory();
      loadFiles();
      showNotify('Semua riwayat dokumen telah dibersihkan.');
    }
  };

  const showNotify = (msg: string) => {
    setNotification(msg);
    setTimeout(() => setNotification(null), 3000);
  };

  const filteredFiles = files.filter((item) => {
    const matchSearch = item.fileName.toLowerCase().includes(search.toLowerCase());
    const matchTool = toolFilter === 'all' || item.tool === toolFilter;
    return matchSearch && matchTool;
  });

  // Calculate statistics
  const totalFiles = files.length;
  const totalOriginalBytes = files.reduce((acc, f) => acc + (f.originalSize || 0), 0);
  const totalProcessedBytes = files.reduce((acc, f) => acc + (f.processedSize || 0), 0);
  const savedBytes = Math.max(0, totalOriginalBytes - totalProcessedBytes);

  const getToolBadge = (tool: ProcessedFile['tool']) => {
    switch (tool) {
      case 'merge':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
            <Layers className="w-3 h-3 text-purple-400" />
            <span>Merge</span>
          </span>
        );
      case 'compress':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
            <Zap className="w-3 h-3 text-emerald-400" />
            <span>Kompresi</span>
          </span>
        );
      case 'pdf-to-word':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
            <FileSpreadsheet className="w-3 h-3 text-blue-400" />
            <span>PDF to Word</span>
          </span>
        );
      case 'ocr':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
            <ScanText className="w-3 h-3 text-amber-400" />
            <span>OCR Teks</span>
          </span>
        );
    }
  };

  return (
    <div className="w-full max-w-7xl mx-auto px-4 py-6">
      {/* Toast Notification */}
      {notification && (
        <div className="fixed bottom-6 right-6 z-50 p-4 rounded-2xl bg-purple-900 border border-pink-500/50 text-white text-xs font-semibold shadow-2xl animate-in slide-in-from-bottom-2 flex items-center gap-2">
          <CheckCircle2 className="w-4 h-4 text-emerald-400" />
          <span>{notification}</span>
        </div>
      )}

      {/* Main Container */}
      <div className="rounded-3xl bg-[#141226]/90 border border-purple-500/20 p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
        {/* Top Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-purple-500/15">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-pink-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-pink-600/30">
              <FolderArchive className="w-6 h-6" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h2 className="text-xl sm:text-2xl font-bold text-white tracking-tight">Manajemen File & Riwayat</h2>
                <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                  Cloud Storage
                </span>
              </div>
              <p className="text-xs sm:text-sm text-purple-300/70">
                Akses semua file yang telah Anda gabungkan, kompres, konversi, atau OCR dari perangkat apa pun.
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={onOpenLaravelModal}
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-pink-500/10 hover:bg-pink-500/20 border border-pink-500/30 text-pink-300 text-xs font-semibold transition-colors"
            >
              <Database className="w-4 h-4 text-pink-400" />
              <span>Skema Database MySQL</span>
            </button>

            {files.length > 0 && (
              <button
                onClick={handleClearAll}
                className="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-white/5 hover:bg-rose-500/15 border border-purple-500/20 text-purple-300/80 hover:text-rose-300 text-xs font-semibold transition-colors"
              >
                <Trash2 className="w-3.5 h-3.5" />
                <span>Bersihkan</span>
              </button>
            )}
          </div>
        </div>

        {/* Dashboard Stat Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 my-6">
          <div className="p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
            <div className="flex items-center justify-between mb-1">
              <span className="text-xs text-purple-300/70">Total Dokumen</span>
              <FileText className="w-4 h-4 text-purple-400" />
            </div>
            <p className="text-2xl font-black text-white">{totalFiles}</p>
            <p className="text-[10px] text-purple-400/60 mt-1">Tersimpan dalam database</p>
          </div>

          <div className="p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
            <div className="flex items-center justify-between mb-1">
              <span className="text-xs text-purple-300/70">Penghematan Ukuran</span>
              <Zap className="w-4 h-4 text-emerald-400" />
            </div>
            <p className="text-2xl font-black text-emerald-400">{formatBytes(savedBytes)}</p>
            <p className="text-[10px] text-emerald-400/70 mt-1">Kapasitas penyimpanan dihemat</p>
          </div>

          <div className="p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
            <div className="flex items-center justify-between mb-1">
              <span className="text-xs text-purple-300/70">Pengguna Aktif</span>
              <HardDrive className="w-4 h-4 text-pink-400" />
            </div>
            <p className="text-2xl font-black text-white">{currentUser?.name?.split(' ')[0] || 'Tamu'}</p>
            <p className="text-[10px] text-purple-300/60 mt-1">{currentUser?.email || 'Sesi Browser'}</p>
          </div>

          <div className="p-4 rounded-2xl bg-[#191632] border border-purple-500/20">
            <div className="flex items-center justify-between mb-1">
              <span className="text-xs text-purple-300/70">Keamanan Dokumen</span>
              <CheckCircle2 className="w-4 h-4 text-blue-400" />
            </div>
            <p className="text-2xl font-black text-white">256-bit</p>
            <p className="text-[10px] text-blue-400/70 mt-1">Enkripsi AES & SSL/TLS</p>
          </div>
        </div>

        {/* Filter & Search Bar */}
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
          {/* Tool categories */}
          <div className="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            {[
              { id: 'all', label: 'Semua' },
              { id: 'merge', label: 'Merge' },
              { id: 'compress', label: 'Kompres' },
              { id: 'pdf-to-word', label: 'PDF to Word' },
              { id: 'ocr', label: 'OCR' }
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setToolFilter(tab.id as any)}
                className={`px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors ${
                  toolFilter === tab.id
                    ? 'bg-purple-600 text-white shadow-md'
                    : 'bg-[#191632] text-purple-300/70 hover:text-white border border-purple-500/20'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </div>

          {/* Search box */}
          <div className="relative w-full sm:w-64">
            <Search className="w-4 h-4 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Cari nama dokumen..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-3 py-2 rounded-xl bg-[#191632] border border-purple-500/20 text-xs text-white placeholder-purple-400/50 focus:outline-none focus:border-pink-500"
            />
          </div>
        </div>

        {/* Files List Table */}
        {filteredFiles.length === 0 ? (
          <div className="text-center py-16 px-4 rounded-2xl bg-[#181530]/40 border border-purple-500/20">
            <FolderArchive className="w-12 h-12 text-purple-400/40 mx-auto mb-3" />
            <h3 className="text-base font-bold text-white mb-1">Belum Ada Dokumen dalam Riwayat</h3>
            <p className="text-xs text-purple-300/70 mb-5 max-w-sm mx-auto">
              Gunakan salah satu alat PDF kami (Merge, Compress, PDF to Word, atau OCR) untuk memproses file pertama Anda.
            </p>
            <div className="flex flex-wrap items-center justify-center gap-2">
              <button
                onClick={() => setActiveTab('merge')}
                className="px-4 py-2 rounded-xl bg-purple-600 text-white text-xs font-semibold hover:bg-purple-500"
              >
                Merge PDF
              </button>
              <button
                onClick={() => setActiveTab('compress')}
                className="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-500"
              >
                Compress PDF
              </button>
              <button
                onClick={() => setActiveTab('pdf-to-word')}
                className="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-500"
              >
                PDF to Word
              </button>
            </div>
          </div>
        ) : (
          <div className="overflow-x-auto rounded-2xl border border-purple-500/20 bg-[#16142c]">
            <table className="w-full text-left text-xs">
              <thead className="bg-[#1b1836] text-purple-300/80 border-b border-purple-500/20 font-semibold uppercase tracking-wider">
                <tr>
                  <th className="py-3.5 px-4">Nama Dokumen</th>
                  <th className="py-3.5 px-3">Alat</th>
                  <th className="py-3.5 px-3">Ukuran File</th>
                  <th className="py-3.5 px-3">Waktu Proses</th>
                  <th className="py-3.5 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-purple-500/10 text-purple-200">
                {filteredFiles.map((file) => (
                  <tr
                    key={file.id}
                    onClick={() => setSelectedFileForModal(file)}
                    className="hover:bg-purple-900/15 transition-colors cursor-pointer"
                  >
                    <td className="py-3.5 px-4">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-lg bg-purple-900/40 border border-purple-500/20 flex items-center justify-center text-pink-400 shrink-0">
                          <FileText className="w-4 h-4" />
                        </div>
                        <div className="truncate max-w-[220px] sm:max-w-[320px]">
                          <p className="font-semibold text-white truncate">{file.fileName}</p>
                          <span className="text-[10px] text-purple-400/60">
                            {file.mimeType.split('/')[1] || 'pdf'}
                          </span>
                        </div>
                      </div>
                    </td>

                    <td className="py-3.5 px-3 whitespace-nowrap">
                      {getToolBadge(file.tool)}
                    </td>

                    <td className="py-3.5 px-3 whitespace-nowrap">
                      <div>
                        <span className="font-medium text-white">{formatBytes(file.processedSize)}</span>
                        {file.reductionPercentage ? (
                          <span className="ml-1.5 text-[10px] font-bold text-emerald-400">
                            (-{file.reductionPercentage}%)
                          </span>
                        ) : null}
                      </div>
                    </td>

                    <td className="py-3.5 px-3 whitespace-nowrap text-purple-300/60 text-[11px]">
                      {new Date(file.createdAt).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                      })}
                    </td>

                    <td className="py-3.5 px-4 text-right whitespace-nowrap">
                      <div className="flex items-center justify-end gap-1.5">
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            setSelectedFileForModal(file);
                          }}
                          className="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-purple-300"
                          title="Lihat Detail Dokumen"
                        >
                          <ExternalLink className="w-3.5 h-3.5" />
                        </button>

                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            showNotify(`Tautan berbagi untuk ${file.fileName} disalin!`);
                          }}
                          className="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-purple-300"
                          title="Bagikan Tautan"
                        >
                          <Share2 className="w-3.5 h-3.5" />
                        </button>

                        <button
                          onClick={(e) => handleDelete(file.id, e)}
                          className="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300"
                          title="Hapus Dokumen"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Detail Modal */}
      {selectedFileForModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm animate-in fade-in">
          <div className="w-full max-w-lg rounded-3xl bg-[#151329] border border-purple-500/30 p-6 sm:p-8 shadow-2xl relative text-left">
            <h3 className="text-lg font-bold text-white mb-2">Detail Dokumen</h3>
            <div className="space-y-3 my-4 text-xs text-purple-200">
              <div className="p-3 rounded-xl bg-[#1c1936] border border-purple-500/20">
                <span className="text-[10px] text-purple-400 block mb-0.5">Nama File:</span>
                <p className="font-semibold text-white break-all">{selectedFileForModal.fileName}</p>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="p-3 rounded-xl bg-[#1c1936] border border-purple-500/20">
                  <span className="text-[10px] text-purple-400 block mb-0.5">Alat Digunakan:</span>
                  <div className="mt-1">{getToolBadge(selectedFileForModal.tool)}</div>
                </div>

                <div className="p-3 rounded-xl bg-[#1c1936] border border-purple-500/20">
                  <span className="text-[10px] text-purple-400 block mb-0.5">Ukuran Akhir:</span>
                  <p className="font-semibold text-emerald-400">{formatBytes(selectedFileForModal.processedSize)}</p>
                </div>
              </div>

              {selectedFileForModal.extractedText && (
                <div className="p-3 rounded-xl bg-[#1c1936] border border-purple-500/20 max-h-40 overflow-y-auto">
                  <span className="text-[10px] text-purple-400 block mb-1">Pratinjau Teks (OCR / Ekstraksi):</span>
                  <p className="text-[11px] font-mono whitespace-pre-wrap text-purple-200">
                    {selectedFileForModal.extractedText}
                  </p>
                </div>
              )}
            </div>

            <div className="flex items-center justify-end gap-2 pt-2">
              <button
                onClick={() => setSelectedFileForModal(null)}
                className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-purple-200"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
