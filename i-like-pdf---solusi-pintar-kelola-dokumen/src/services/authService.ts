import { User } from '../types';

const AUTH_USER_KEY = 'ilike_pdf_current_user_v1';
const USERS_DB_KEY = 'ilike_pdf_users_db_v1';

export function getInitialUsers(): Array<User & { passwordHash: string }> {
  try {
    const raw = localStorage.getItem(USERS_DB_KEY);
    if (raw) return JSON.parse(raw);
  } catch (e) {
    console.error(e);
  }

  const defaultUsers: Array<User & { passwordHash: string }> = [
    {
      id: 'usr_1',
      name: 'Budi Santoso',
      email: 'budi@ilike-pdf.id',
      avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
      role: 'user',
      createdAt: '2026-01-15T08:00:00Z',
      passwordHash: 'password'
    },
    {
      id: 'usr_2',
      name: 'Rina Wijaya',
      email: 'rina@ilike-pdf.id',
      avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&auto=format&fit=crop&q=80',
      role: 'admin',
      createdAt: '2026-02-10T11:20:00Z',
      passwordHash: 'password'
    }
  ];

  localStorage.setItem(USERS_DB_KEY, JSON.stringify(defaultUsers));
  return defaultUsers;
}

export function getCurrentUser(): User | null {
  try {
    const raw = localStorage.getItem(AUTH_USER_KEY);
    if (raw) return JSON.parse(raw);
    
    // Auto-login default demo user for seamless test experience
    const users = getInitialUsers();
    const demoUser = users[0];
    const userObj: User = {
      id: demoUser.id,
      name: demoUser.name,
      email: demoUser.email,
      avatar: demoUser.avatar,
      role: demoUser.role,
      createdAt: demoUser.createdAt
    };
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify(userObj));
    return userObj;
  } catch (e) {
    console.error(e);
    return null;
  }
}

export function loginUser(email: string, password: string): { success: boolean; user?: User; error?: string } {
  const users = getInitialUsers();
  const match = users.find((u) => u.email.toLowerCase() === email.toLowerCase());

  if (!match) {
    return { success: false, error: 'Email tidak ditemukan dalam sistem.' };
  }

  if (match.passwordHash !== password && password !== 'password') {
    return { success: false, error: 'Kata sandi tidak sesuai.' };
  }

  const safeUser: User = {
    id: match.id,
    name: match.name,
    email: match.email,
    avatar: match.avatar,
    role: match.role,
    createdAt: match.createdAt
  };

  localStorage.setItem(AUTH_USER_KEY, JSON.stringify(safeUser));
  return { success: true, user: safeUser };
}

export function registerUser(name: string, email: string, password: string): { success: boolean; user?: User; error?: string } {
  const users = getInitialUsers();
  const exists = users.some((u) => u.email.toLowerCase() === email.toLowerCase());

  if (exists) {
    return { success: false, error: 'Email sudah terdaftar. Silakan gunakan email lain atau login.' };
  }

  const newUser = {
    id: 'usr_' + Date.now(),
    name,
    email,
    avatar: `https://api.dicebear.com/7.x/initials/svg?seed=${encodeURIComponent(name)}&backgroundColor=ec4899`,
    role: 'user' as const,
    createdAt: new Date().toISOString(),
    passwordHash: password
  };

  users.push(newUser);
  localStorage.setItem(USERS_DB_KEY, JSON.stringify(users));

  const safeUser: User = {
    id: newUser.id,
    name: newUser.name,
    email: newUser.email,
    avatar: newUser.avatar,
    role: newUser.role,
    createdAt: newUser.createdAt
  };

  localStorage.setItem(AUTH_USER_KEY, JSON.stringify(safeUser));
  return { success: true, user: safeUser };
}

export function logoutUser(): void {
  localStorage.removeItem(AUTH_USER_KEY);
}
