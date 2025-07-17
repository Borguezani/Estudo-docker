<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MovieList;
use App\Models\MovieListItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MovieListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário de teste
        $user = User::firstOrCreate(
            ['email' => 'teste@exemplo.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('senha123'),
            ]
        );

        // Criar listas de exemplo
        $favoritesMovie = MovieList::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => 'Filmes Favoritos'
            ],
            [
                'description' => 'Minha lista pessoal de filmes favoritos de todos os tempos',
                'is_public' => true,
            ]
        );

        $watchLater = MovieList::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => 'Assistir Depois'
            ],
            [
                'description' => 'Filmes que quero assistir quando tiver tempo',
                'is_public' => false,
            ]
        );

        $actionMovies = MovieList::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => 'Melhores Filmes de Ação'
            ],
            [
                'description' => 'Os melhores filmes de ação que já assisti',
                'is_public' => true,
            ]
        );

        // Filmes de exemplo para adicionar às listas
        $sampleMovies = [
            [
                'tmdb_movie_id' => 550,
                'movie_title' => 'Clube da Luta',
                'movie_poster_path' => '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'movie_overview' => 'Um funcionário de escritório insone e um fabricante de sabão formam um clube de luta clandestino.',
                'movie_release_date' => '1999-10-15',
                'movie_vote_average' => 8.4,
            ],
            [
                'tmdb_movie_id' => 155,
                'movie_title' => 'Batman Begins',
                'movie_poster_path' => '/8RW2runSEc34BwKTN7H5pCl0yOD.jpg',
                'movie_overview' => 'Após treinar com seu mentor, Batman inicia sua luta para libertar Gotham City da corrupção.',
                'movie_release_date' => '2005-06-10',
                'movie_vote_average' => 7.7,
            ],
            [
                'tmdb_movie_id' => 13,
                'movie_title' => 'Forrest Gump',
                'movie_poster_path' => '/arw2vcBveWOVZr6pxd9XTd1TdQa.jpg',
                'movie_overview' => 'Forrest Gump narra várias décadas de sua vida que coincidiram com importantes eventos históricos.',
                'movie_release_date' => '1994-06-23',
                'movie_vote_average' => 8.5,
            ],
            [
                'tmdb_movie_id' => 680,
                'movie_title' => 'Pulp Fiction',
                'movie_poster_path' => '/dM2w364MScsjFf8pfMbaWUcWrR.jpg',
                'movie_overview' => 'As vidas de dois assassinos da máfia, um boxeador e um casal de bandidos se entrelaçam.',
                'movie_release_date' => '1994-09-10',
                'movie_vote_average' => 8.5,
            ],
            [
                'tmdb_movie_id' => 27205,
                'movie_title' => 'A Origem',
                'movie_poster_path' => '/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
                'movie_overview' => 'Dom Cobb é um ladrão especializado em extrair segredos do subconsciente durante o sono.',
                'movie_release_date' => '2010-07-15',
                'movie_vote_average' => 8.4,
            ],
        ];

        // Adicionar filmes às listas
        foreach ($sampleMovies as $index => $movie) {
            // Adicionar aos favoritos (todos)
            MovieListItem::firstOrCreate(
                [
                    'movie_list_id' => $favoritesMovie->id,
                    'tmdb_movie_id' => $movie['tmdb_movie_id']
                ],
                array_merge($movie, [
                    'user_notes' => 'Um dos meus filmes favoritos de todos os tempos!'
                ])
            );

            // Adicionar filmes de ação específicos
            if (in_array($movie['tmdb_movie_id'], [155, 27205])) {
                MovieListItem::firstOrCreate(
                    [
                        'movie_list_id' => $actionMovies->id,
                        'tmdb_movie_id' => $movie['tmdb_movie_id']
                    ],
                    array_merge($movie, [
                        'user_notes' => 'Ação incrível e efeitos especiais fantásticos!'
                    ])
                );
            }

            // Adicionar alguns à lista "Assistir Depois"
            if ($index < 2) {
                MovieListItem::firstOrCreate(
                    [
                        'movie_list_id' => $watchLater->id,
                        'tmdb_movie_id' => $movie['tmdb_movie_id']
                    ],
                    array_merge($movie, [
                        'user_notes' => 'Preciso reassistir este filme!'
                    ])
                );
            }
        }

        $this->command->info('Dados de teste criados com sucesso!');
        $this->command->info('Usuário: teste@exemplo.com');
        $this->command->info('Senha: senha123');
        $this->command->info('Listas criadas: ' . $user->movieLists()->count());
    }
}
