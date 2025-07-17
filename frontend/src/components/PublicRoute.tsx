import { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../contexts/AuthorizationContext';

interface PublicRouteProps {
  children: React.ReactNode;
}

export default function PublicRoute({ children }: PublicRouteProps) {
  const { isAuth, isLoading } = useContext(AuthContext);

  // Se estiver carregando, mostrar loading
  if (isLoading) {
    return (
      <div style={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        height: '100vh',
        fontSize: '18px'
      }}>
        Carregando...
      </div>
    );
  }

  // Se estiver autenticado, redirecionar para home
  if (isAuth) {
    return <Navigate to="/home" replace />;
  }

  return <>{children}</>;
}
