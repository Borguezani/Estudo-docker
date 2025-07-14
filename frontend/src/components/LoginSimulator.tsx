import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthorizationContext';

const LoginSimulator: React.FC = () => {
  const [isSimulating, setIsSimulating] = useState(false);
  const { updateToken } = useAuth();

  // Simular login com dados fake (para desenvolvimento/teste)
  const simulateLogin = async (role: 'admin' | 'user' = 'user') => {
    setIsSimulating(true);
    
    // Simular delay de rede
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Criar um payload JWT fake
    const fakePayload = {
      nome: role === 'admin' ? 'Admin User' : 'Regular User',
      email: role === 'admin' ? 'admin@example.com' : 'user@example.com',
      exp: Math.floor(Date.now() / 1000) + (60 * 60), // 1 hora
    };
    
    // Simular um JWT token (base64 encoded payload)
    const header = btoa(JSON.stringify({ alg: 'HS256', typ: 'JWT' }));
    const payload = btoa(JSON.stringify(fakePayload));
    const signature = 'fake_signature';
    const fakeToken = `${header}.${payload}.${signature}`;
    
    // Atualizar contexto com o token fake
    updateToken(fakeToken);
    
    setIsSimulating(false);
  };

  return (
    <div style={{ 
      padding: '20px',
      backgroundColor: '#e3f2fd',
      border: '1px solid #2196f3',
      borderRadius: '8px',
      margin: '20px 0'
    }}>
      <h3>🧪 Simulador de Login (Desenvolvimento)</h3>
      <p>Use estes botões para simular login sem precisar de uma API real:</p>
      
      <div style={{ display: 'flex', gap: '10px', marginTop: '15px' }}>
        <button
          onClick={() => simulateLogin('user')}
          disabled={isSimulating}
          style={{
            padding: '10px 15px',
            backgroundColor: '#4caf50',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: isSimulating ? 'not-allowed' : 'pointer',
            opacity: isSimulating ? 0.6 : 1
          }}
        >
          {isSimulating ? 'Simulando...' : 'Login como Usuário'}
        </button>
        
        <button
          onClick={() => simulateLogin('admin')}
          disabled={isSimulating}
          style={{
            padding: '10px 15px',
            backgroundColor: '#ff9800',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: isSimulating ? 'not-allowed' : 'pointer',
            opacity: isSimulating ? 0.6 : 1
          }}
        >
          {isSimulating ? 'Simulando...' : 'Login como Admin'}
        </button>
      </div>
      
      <p style={{ 
        fontSize: '12px', 
        color: '#666', 
        marginTop: '10px',
        fontStyle: 'italic'
      }}>
        * Este simulador é apenas para desenvolvimento. Em produção, remova este componente.
      </p>
    </div>
  );
};

export default LoginSimulator;
