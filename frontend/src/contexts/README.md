# AuthorizationContext

Um sistema de autenticação e autorização simples para React com TypeScript.

## Funcionalidades

- ✅ Login e logout
- ✅ Registro de usuários
- ✅ Persistência no localStorage
- ✅ Verificação de roles/permissões
- ✅ Componentes protegidos
- ✅ Hooks personalizados
- ✅ Loading states
- ✅ Error handling

## Como usar

### 1. Envolver a aplicação com o Provider

```tsx
import { AuthorizationProvider } from './contexts/AutorizationContex';

function App() {
  return (
    <AuthorizationProvider>
      {/* Sua aplicação aqui */}
    </AuthorizationProvider>
  );
}
```

### 2. Usar os hooks nos componentes

```tsx
import { useAuthorization, useIsAuthenticated, useHasRole } from './contexts/AutorizationContex';

function MeuComponente() {
  const { user, login, logout, isLoading } = useAuthorization();
  const isAuthenticated = useIsAuthenticated();
  const isAdmin = useHasRole('admin');

  // Fazer login
  const handleLogin = async () => {
    const success = await login('email@example.com', 'senha123');
    if (success) {
      console.log('Login realizado com sucesso!');
    }
  };

  // Fazer logout
  const handleLogout = () => {
    logout();
  };

  return (
    <div>
      {isAuthenticated ? (
        <div>
          <p>Bem-vindo, {user?.name}!</p>
          {isAdmin && <p>Você é um administrador</p>}
          <button onClick={handleLogout}>Sair</button>
        </div>
      ) : (
        <button onClick={handleLogin}>Entrar</button>
      )}
    </div>
  );
}
```

### 3. Proteger rotas

```tsx
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <div>
      {/* Rota pública */}
      <Login />
      
      {/* Rota protegida */}
      <ProtectedRoute>
        <Dashboard />
      </ProtectedRoute>
      
      {/* Rota protegida com fallback customizado */}
      <ProtectedRoute fallback={<div>Você precisa estar logado</div>}>
        <AdminPanel />
      </ProtectedRoute>
    </div>
  );
}
```

## API do Backend

O contexto espera que sua API tenha os seguintes endpoints:

### POST /api/login
```json
// Requisição
{
  "email": "user@example.com",
  "password": "senha123"
}

// Resposta (200 OK)
{
  "user": {
    "id": "1",
    "name": "Nome do Usuário",
    "email": "user@example.com",
    "role": "admin"
  },
  "token": "jwt_token_aqui"
}
```

### POST /api/register
```json
// Requisição
{
  "name": "Nome do Usuário",
  "email": "user@example.com",
  "password": "senha123"
}

// Resposta (201 Created)
{
  "user": {
    "id": "1",
    "name": "Nome do Usuário",
    "email": "user@example.com",
    "role": "user"
  },
  "token": "jwt_token_aqui"
}
```

## Hooks disponíveis

### useAuthorization()
Retorna o contexto completo com todas as funções e estado.

### useIsAuthenticated()
Retorna apenas um boolean indicando se o usuário está autenticado.

### useHasRole(role: string)
Verifica se o usuário tem uma role específica.

## Configuração

Para alterar a URL da API, edite as URLs nos métodos `login` e `register` no arquivo `AutorizationContex.tsx`:

```tsx
// Altere estas URLs para apontar para sua API
const response = await fetch('http://localhost:8000/api/login', {
  // ...
});
```

## Exemplo de uso com formulário

```tsx
import React, { useState } from 'react';
import { useAuthorization } from './contexts/AutorizationContex';

const LoginForm = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const { login, isLoading } = useAuthorization();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const success = await login(email, password);
    
    if (success) {
      console.log('Login realizado com sucesso!');
    } else {
      console.log('Credenciais inválidas');
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
        disabled={isLoading}
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Senha"
        disabled={isLoading}
      />
      <button type="submit" disabled={isLoading}>
        {isLoading ? 'Entrando...' : 'Entrar'}
      </button>
    </form>
  );
};
```
