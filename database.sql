-- ============================================
-- FitZone - Estrutura do Banco de Dados
-- ============================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS fitzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitzone;

-- ============================================
-- Tabela: users
-- ============================================
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: exercises (Exercícios)
-- ============================================
CREATE TABLE `exercises` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `muscle_group` varchar(255) NOT NULL COMMENT 'Grupo muscular: Peito, Costas, Pernas, etc.',
  `equipment` varchar(255) DEFAULT NULL COMMENT 'Equipamento necessário',
  `video_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercises_muscle_group_index` (`muscle_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: workouts (Treinos)
-- ============================================
CREATE TABLE `workouts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Ex: Treino A - Peito e Tríceps',
  `description` text DEFAULT NULL,
  `focus` varchar(255) NOT NULL COMMENT 'Foco: Hipertrofia, Força, Resistência',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workouts_user_id_foreign` (`user_id`),
  CONSTRAINT `workouts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: workout_exercises (Relação Treino-Exercício)
-- ============================================
CREATE TABLE `workout_exercises` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workout_id` bigint(20) UNSIGNED NOT NULL,
  `exercise_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Ordem do exercício no treino',
  `sets` int(11) NOT NULL COMMENT 'Número de séries',
  `reps` varchar(255) NOT NULL COMMENT 'Repetições: pode ser 10, 10-12, até a falha',
  `weight` decimal(8,2) DEFAULT NULL COMMENT 'Carga em kg',
  `rest_time` int(11) DEFAULT NULL COMMENT 'Tempo de descanso em segundos',
  `notes` text DEFAULT NULL COMMENT 'Observações',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workout_exercises_workout_id_foreign` (`workout_id`),
  KEY `workout_exercises_exercise_id_foreign` (`exercise_id`),
  CONSTRAINT `workout_exercises_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_exercises_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: workout_plans (Plano Semanal)
-- ============================================
CREATE TABLE `workout_plans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `workout_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` enum('Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo') NOT NULL,
  `scheduled_time` time DEFAULT NULL COMMENT 'Horário planejado',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workout_plans_user_id_foreign` (`user_id`),
  KEY `workout_plans_workout_id_foreign` (`workout_id`),
  KEY `workout_plans_day_of_week_index` (`day_of_week`),
  CONSTRAINT `workout_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_plans_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabelas do Laravel (Sistema)
-- ============================================

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DADOS INICIAIS
-- ============================================

-- Inserir usuário padrão (senha: password)
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`) VALUES
('FitZone User', 'user@fitzone.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5D/s8xRmw8Wm2', NOW(), NOW());

-- Inserir exercícios pré-cadastrados
INSERT INTO `exercises` (`name`, `description`, `muscle_group`, `equipment`, `created_at`, `updated_at`) VALUES
-- PEITO
('Supino Reto', 'Exercício básico para desenvolvimento do peitoral', 'Peito', 'Barra/Halteres', NOW(), NOW()),
('Supino Inclinado', 'Foca na parte superior do peitoral', 'Peito', 'Barra/Halteres', NOW(), NOW()),
('Supino Declinado', 'Foca na parte inferior do peitoral', 'Peito', 'Barra/Halteres', NOW(), NOW()),
('Crucifixo', 'Exercício de isolamento para peitoral', 'Peito', 'Halteres/Cabos', NOW(), NOW()),
('Flexão de Braço', 'Exercício com peso corporal para peitoral', 'Peito', 'Peso Corporal', NOW(), NOW()),

-- COSTAS
('Barra Fixa', 'Exercício composto para desenvolvimento das costas', 'Costas', 'Barra Fixa', NOW(), NOW()),
('Remada Curvada', 'Exercício para espessura das costas', 'Costas', 'Barra/Halteres', NOW(), NOW()),
('Remada Cavalinho', 'Exercício para região lombar e dorsais', 'Costas', 'Máquina', NOW(), NOW()),
('Pulldown', 'Exercício para largura das costas', 'Costas', 'Cabo', NOW(), NOW()),
('Levantamento Terra', 'Exercício composto para costas e posterior', 'Costas', 'Barra', NOW(), NOW()),

-- PERNAS
('Agachamento', 'Exercício base para desenvolvimento de pernas', 'Pernas', 'Barra/Livre', NOW(), NOW()),
('Leg Press', 'Exercício para quadríceps e glúteos', 'Pernas', 'Máquina', NOW(), NOW()),
('Cadeira Extensora', 'Isolamento de quadríceps', 'Pernas', 'Máquina', NOW(), NOW()),
('Cadeira Flexora', 'Isolamento de posterior de coxa', 'Pernas', 'Máquina', NOW(), NOW()),
('Stiff', 'Exercício para posterior de coxa e glúteos', 'Pernas', 'Barra/Halteres', NOW(), NOW()),
('Panturrilha em Pé', 'Exercício para panturrilhas', 'Pernas', 'Máquina/Livre', NOW(), NOW()),

-- OMBROS
('Desenvolvimento com Barra', 'Exercício composto para ombros', 'Ombros', 'Barra', NOW(), NOW()),
('Desenvolvimento com Halteres', 'Exercício para ombros com amplitude maior', 'Ombros', 'Halteres', NOW(), NOW()),
('Elevação Lateral', 'Isolamento para deltoide lateral', 'Ombros', 'Halteres/Cabos', NOW(), NOW()),
('Elevação Frontal', 'Isolamento para deltoide anterior', 'Ombros', 'Halteres/Barra', NOW(), NOW()),
('Remada Alta', 'Exercício para trapézio e ombros', 'Ombros', 'Barra/Halteres', NOW(), NOW()),

-- BÍCEPS
('Rosca Direta', 'Exercício básico para bíceps', 'Bíceps', 'Barra/Halteres', NOW(), NOW()),
('Rosca Alternada', 'Exercício unilateral para bíceps', 'Bíceps', 'Halteres', NOW(), NOW()),
('Rosca Scott', 'Exercício isolado para bíceps', 'Bíceps', 'Barra/Halteres', NOW(), NOW()),
('Rosca Martelo', 'Exercício para bíceps e antebraço', 'Bíceps', 'Halteres', NOW(), NOW()),

-- TRÍCEPS
('Tríceps Testa', 'Exercício para massa de tríceps', 'Tríceps', 'Barra/Halteres', NOW(), NOW()),
('Tríceps Pulley', 'Exercício para definição de tríceps', 'Tríceps', 'Cabo', NOW(), NOW()),
('Tríceps Francês', 'Exercício para alongamento do tríceps', 'Tríceps', 'Halteres/Barra', NOW(), NOW()),
('Mergulho', 'Exercício com peso corporal para tríceps', 'Tríceps', 'Paralelas', NOW(), NOW()),

-- ABDÔMEN
('Abdominal Supra', 'Exercício para parte superior do abdômen', 'Abdômen', 'Peso Corporal', NOW(), NOW()),
('Abdominal Infra', 'Exercício para parte inferior do abdômen', 'Abdômen', 'Peso Corporal', NOW(), NOW()),
('Prancha', 'Exercício isométrico para core', 'Abdômen', 'Peso Corporal', NOW(), NOW()),
('Abdominal Oblíquo', 'Exercício para oblíquos', 'Abdômen', 'Peso Corporal', NOW(), NOW());

-- ============================================
-- EXEMPLO DE TREINO COMPLETO
-- ============================================

-- Inserir treino exemplo
INSERT INTO `workouts` (`user_id`, `name`, `description`, `focus`, `created_at`, `updated_at`) VALUES
(1, 'Treino A - Peito e Tríceps', 'Treino focado em hipertrofia do peitoral e tríceps', 'Hipertrofia', NOW(), NOW());

-- Adicionar exercícios ao treino (usando IDs dos exercícios inseridos acima)
INSERT INTO `workout_exercises` (`workout_id`, `exercise_id`, `order`, `sets`, `reps`, `weight`, `rest_time`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, '8-10', 80.00, 90, NOW(), NOW()),  -- Supino Reto
(1, 2, 2, 4, '10-12', 60.00, 90, NOW(), NOW()), -- Supino Inclinado
(1, 4, 3, 3, '12-15', 20.00, 60, NOW(), NOW()), -- Crucifixo
(1, 26, 4, 4, '10-12', 40.00, 60, NOW(), NOW()), -- Tríceps Testa
(1, 27, 5, 3, '12-15', NULL, 60, NOW(), NOW()); -- Tríceps Pulley

-- Adicionar treino ao plano semanal
INSERT INTO `workout_plans` (`user_id`, `workout_id`, `day_of_week`, `scheduled_time`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Segunda', '07:00:00', 1, NOW(), NOW());

-- ============================================
-- FIM DO SCRIPT
-- ============================================
