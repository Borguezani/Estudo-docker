
import React from 'react';
import { useAuth } from '../contexts/AuthorizationContext';
import { useNavigate } from 'react-router-dom';

export default function Home() {
  const { getUserData, logout, token } = useAuth();
  const navigate = useNavigate();
  const userData = getUserData();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div style={{ maxWidth: '600px', margin: '50px auto', padding: '20px' }}>
      <div style={{ 
        background: '#f8f9fa', 
        padding: '20px', 
        borderRadius: '8px',
        marginBottom: '20px'
      }}>
        <h1>🎉 Login realizado com sucesso!</h1>
        <p>Bem-vindo à área protegida da aplicação.</p>
      </div>

      {userData && (
        <div style={{ 
          background: '#e8f5e8', 
          padding: '15px', 
          borderRadius: '6px',
          marginBottom: '20px'
        }}>
          <h3>Dados do usuário:</h3>
          <p><strong>Nome:</strong> {userData.name}</p>
          <p><strong>Email:</strong> {userData.email}</p>
        </div>
      )}

      <div style={{ 
        background: '#fff3cd', 
        padding: '15px', 
        borderRadius: '6px',
        marginBottom: '20px'
      }}>
        <h4>Token JWT (primeiros 50 caracteres):</h4>
        <code style={{ fontSize: '12px', wordBreak: 'break-all' }}>
          {token ? token.substring(0, 50) + '...' : 'Não encontrado'}
        </code>
      </div>

      <button
        onClick={handleLogout}
        style={{
          padding: '10px 20px',
          backgroundColor: '#dc3545',
          color: 'white',
          border: 'none',
          borderRadius: '4px',
          cursor: 'pointer'
        }}
      >
        Logout
      </button>
    </div>
  );
}

