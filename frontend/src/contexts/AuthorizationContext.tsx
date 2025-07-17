import {
  ReactNode,
  createContext,
  useMemo,
  useState,
  useCallback,
  useContext,
  useEffect,
} from "react";
import { jwtDecode } from "jwt-decode";
import axiosInstance from "../lib/axios";

export interface UserData {
  id: string;
  name: string;
  email: string;
}

export interface AuthContextType {
  isAuth: boolean;
  isLoading: boolean;
  updateToken: (token: string) => void;
  logout: () => void;
  getUserData: () => UserData | null;
  token: string | null;
}

export const AuthContext = createContext<AuthContextType>({
  isAuth: false,
  isLoading: false,
  updateToken: () => { },
  logout: () => null,
  getUserData: () => null,
  token: "",
});

export function AuthProvider({ children }: { children: ReactNode }) {
  const [token, setToken] = useState<string | null>(
    localStorage.getItem('token') || null
  );
  const [userData, setUserData] = useState<UserData | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(!!localStorage.getItem('token')); // Inicia loading se houver token

  const isAuth = token ? true : false;
  
  const logout = useCallback(() => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    setUserData(null);
    setToken(null);
    setIsLoading(false);
    // Removido window.location.href para evitar reload completo da página
    // O redirecionamento será feito pelos componentes de rota
  }, []);

  const getUserData = useCallback(() => {
    return userData;
  }, [userData]);

  const updateToken = useCallback((newToken: string) => {
     
    localStorage.setItem('token', newToken);
    setToken(newToken);
  }, []);

  // Efeito para buscar dados do usuário quando o token muda
  useEffect(() => {
     
    if (token) {
      setIsLoading(true);
      // Primeiro verificar se o token é válido
      try {
        const decoded: any = jwtDecode(token);
        
        // Verificar se o token não expirou
        const currentTime = Date.now() / 1000;
        if (decoded.exp && decoded.exp < currentTime) {
          console.warn('⚠️ Token expirado');
          logout();
          return;
        }

        // Buscar dados do usuário da API
        const fetchUserData = async () => {
          try {
            
            const response = await axiosInstance.post('/auth/me');
            
            setUserData({
              id: response.data.id || response.data.user?.id || decoded.sub,
              name: response.data.name || response.data.user?.name || '',
              email: response.data.email || response.data.user?.email || ''
            });
          } catch (error: any) {
            console.error('❌ Erro ao buscar dados do usuário:', error);
            console.error('❌ Status do erro:', error.response?.status);
            console.error('❌ Dados do erro:', error.response?.data);
            
            // Só fazer logout se for erro de autenticação (401) ou token inválido
            if (error.response?.status === 401 || error.response?.status === 403) {
               
              logout();
            } else {
              console.error('⚠️ Erro desconhecido, mantendo token mas sem dados do usuário');
              // Para outros erros (rede, servidor), manter o token mas sem dados
              setUserData(null);
            }
          } finally {
            setIsLoading(false);
          }
        };

        fetchUserData();
      } catch (error) {
        console.error('❌ Erro ao decodificar token:', error);
        logout();
        setIsLoading(false);
      }
    } else {
      setUserData(null);
      setIsLoading(false);
    }
  }, [token, logout]);

  const value = useMemo(() => ({
    isAuth,
    isLoading,
    updateToken,
    logout,
    getUserData,
    token,
  }), [isAuth, isLoading, updateToken, logout, getUserData, token]);

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

// Hook personalizado para usar o contexto
export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);

  if (context === undefined) {
    throw new Error('useAuth deve ser usado dentro de um AuthProvider');
  }

  return context;
};

// Hook para verificar se o usuário está autenticado
export const useIsAuthenticated = (): boolean => {
  const { isAuth } = useAuth();
  return isAuth;
};