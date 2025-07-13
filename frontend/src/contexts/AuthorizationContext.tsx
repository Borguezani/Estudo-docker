import {
  ReactNode,
  createContext,
  useMemo,
  useState,
  useCallback,
  useContext,
} from "react";
import { jwtDecode } from "jwt-decode";
import Cookies from "js-cookie";

export interface UserData {
  nome: string;
  email: string;
}

export interface AuthContextType {
  isAuth: boolean;
  updateToken: (token: string) => void;
  logout: () => void;
  getUserData: () => UserData | null;
  token: string | null;
}

export const AuthContext = createContext<AuthContextType>({
  isAuth: false,
  updateToken: () => {},
  logout: () => null,
  getUserData: () => null,
  token: "",
});

export function AuthProvider({ children }: { children: ReactNode }) {
  const [token, setToken] = useState<string | null>(
    Cookies.get("token") || null
  );

  const isAuth = token ? true : false;

  const logout = useCallback(() => {
    Cookies.remove("token");
    setToken(null);
  }, []);

  const updateToken = useCallback((token: string) => {
    const expirationDate = getExpirationDate();
    Cookies.set("token", token, { expires: expirationDate });
    setToken(token);
  }, []);

  const getUserData = useCallback(() => {
    if (token) {
      try {
        let decoded: any = jwtDecode(token);
        return {
          nome: decoded.nome || decoded.name || decoded.usuario?.nome,
          email: decoded.email || decoded.usuario?.email,
        };
      } catch (error) {
        console.error("Erro ao decodificar token:", error);
        return null;
      }
    }
    return null;
  }, [token]);

  const value = useMemo(() => ({
    isAuth,
    updateToken,
    logout,
    getUserData,
    token,
  }), [isAuth, updateToken, logout, getUserData, token]);

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

const getExpirationDate = () => {
  const expirationDate = new Date();
  expirationDate.setHours(23, 59, 59, 999);
  return expirationDate;
};

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