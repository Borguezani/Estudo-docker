import { useContext } from 'react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { createTheme, ThemeProvider } from '@mui/material';
import { AuthContext, AuthProvider, useIsAuthenticated } from './contexts/AuthorizationContext';
import { createBrowserRouter, RouterProvider } from 'react-router-dom';
import protectedRoutes from './routes/protectedRoutes';
import publicRoutes from './routes/publicRoutes';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
    },
  },
});

const defaultTheme = createTheme({
palette: {
  background: {
    default: '#09090b'
  },
  primary: {
    main: '#fafafa',
  },
  secondary: {
    main: '#40ff96ff'
  }
}
});

// Componente interno que usa o contexto
function AppRouter() {
  const { isAuth } = useContext(AuthContext);

  const router = createBrowserRouter([
    ...(isAuth ? protectedRoutes() : []), 
    ...publicRoutes(),
  ]);

  return <RouterProvider router={router} />;
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <ThemeProvider theme={defaultTheme}>
        <AuthProvider>
          <AppRouter />
        </AuthProvider>
      </ThemeProvider>
    </QueryClientProvider>
  );
}

export default App;
