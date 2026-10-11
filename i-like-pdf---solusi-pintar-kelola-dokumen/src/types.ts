export type ActiveTab = 'home' | 'merge' | 'compress' | 'pdf-to-word' | 'ocr' | 'files' | 'laravel-backend';

export interface User {
  id: string;
  name: string;
  email: string;
  avatar?: string;
  role: 'user' | 'admin';
  createdAt: string;
}

export interface ProcessedFile {
  id: string;
  userId?: string;
  fileName: string;
  tool: 'merge' | 'compress' | 'pdf-to-word' | 'ocr';
  originalSize: number;
  processedSize: number;
  reductionPercentage?: number;
  createdAt: string;
  downloadUrl?: string;
  blobData?: Blob;
  mimeType: string;
  extractedText?: string;
  pageCount?: number;
}

export interface CompressionOption {
  id: 'extreme' | 'recommended' | 'light';
  name: string;
  reductionLabel: string;
  reductionRatio: number;
  description: string;
  dpi: number;
}
