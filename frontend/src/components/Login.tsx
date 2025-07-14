import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthorizationContext';
import LoginSimulator from './LoginSimulator';
import axiosInstance from '../lib/axios';
import axios from 'axios';

const Login: React.FC = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const { updateToken } = useAuth();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setIsLoading(true);

        try {
            // Fazer a requisição de teste

            const tokenResponse = await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
                withCredentials: true, // Necessário para enviar cookies CSRF
            });
            // const response = await axios.post('http://localhost:8000/api/test', {
            //     test: 'data'
            // }, {
            //     withCredentials: true,
            // });
            // Fazer a requisição de login
            const response = await axios.post('http://localhost:8000/api/auth/login', {
                email,
                password
            },
            {
                withCredentials: true, // Necessário para enviar cookies CSRF
            }
        );

            if (response.data.success && response.data.data.token) {
                // Salvar o token retornado pela API
                updateToken(response.data.data.token);
                // O React Router redirecionará automaticamente para as rotas protegidas
                // após a atualização do token
            } else {
                console.log('Estrutura de resposta inesperada:', response.data);
                setError('Resposta inválida do servidor');
            }
        } catch (error: any) {
            console.error('Erro no login:', error);

            if (error.response) {
                // Erro de resposta da API
                const { status, data } = error.response;
                if (status === 401) {
                    setError('Email ou senha inválidos');
                } else if (status === 422) {
                    setError('Dados inválidos. Verifique email e senha.');
                } else if (data && data.message) {
                    setError(data.message);
                } else {
                    setError('Erro do servidor. Tente novamente.');
                }
            } else if (error.request) {
                // Erro de rede/conectividade
                setError('Erro ao conectar com o servidor. Verifique sua conexão.');
            } else {
                setError('Erro inesperado. Tente novamente.');
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px' }}>
            <LoginSimulator />

            <h2>Login</h2>
            <form onSubmit={handleSubmit}>
                <div style={{ marginBottom: '15px' }}>
                    <label htmlFor="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        style={{
                            width: '100%',
                            padding: '8px',
                            marginTop: '5px',
                            border: '1px solid #ccc',
                            borderRadius: '4px'
                        }}
                        disabled={isLoading}
                    />
                </div>

                <div style={{ marginBottom: '15px' }}>
                    <label htmlFor="password">Senha:</label>
                    <input
                        type="password"
                        id="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        style={{
                            width: '100%',
                            padding: '8px',
                            marginTop: '5px',
                            border: '1px solid #ccc',
                            borderRadius: '4px'
                        }}
                        disabled={isLoading}
                    />
                </div>

                {error && (
                    <div style={{
                        color: 'red',
                        marginBottom: '15px',
                        padding: '8px',
                        backgroundColor: '#ffebee',
                        border: '1px solid #ffcdd2',
                        borderRadius: '4px'
                    }}>
                        {error}
                    </div>
                )}

                <button
                    type="submit"
                    disabled={isLoading}
                    style={{
                        width: '100%',
                        padding: '10px',
                        backgroundColor: isLoading ? '#ccc' : '#007bff',
                        color: 'white',
                        border: 'none',
                        borderRadius: '4px',
                        cursor: isLoading ? 'not-allowed' : 'pointer'
                    }}
                >
                    {isLoading ? 'Entrando...' : 'Entrar'}
                </button>
            </form>
        </div>
    );
};

export default Login;
