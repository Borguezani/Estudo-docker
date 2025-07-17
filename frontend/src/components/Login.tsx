import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthorizationContext';
import axiosInstance from '../lib/axios';
import { AxiosResponse } from 'axios';
import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';

const Login: React.FC = () => {

    const { updateToken } = useAuth();
    const navigate = useNavigate();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const { mutate: autenticar, isPending: isLoading } = useMutation({
        mutationFn: (inputs: LoginData) => axiosInstance.post("/auth/login", inputs),
        onSuccess: (response : AxiosResponse<AuthResponse>) => {
            console.log('Resposta do login:', response.data);
            updateToken(response.data.access_token); // Correção: usar access_token ao invés de token_type
            navigate('/home');
        },
        onError: (error: any) => {
            setError(error.response?.data?.message || 'Erro ao fazer login');
        }
    });

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        autenticar({ email, password });
    };

    return (
        <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px' }}>

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

interface LoginData {
    email: string
    password: string
}

export interface AuthResponse {
    access_token: string
    expires_in: number
    token_type: string
    user: {
        id: string
        name: string
        email: string
    }
}

export default Login;
