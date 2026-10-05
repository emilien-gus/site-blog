-- Устанавливаем правильную кодировку для поддержки любых символов
SET NAMES utf8mb4;

-- Таблица категорий
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица статей
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    image VARCHAR(255) DEFAULT NULL, -- Храним относительный путь к картинке
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Промежуточная таблица для связи "Многие ко многим"
CREATE TABLE IF NOT EXISTS article_category (
    article_id INT NOT NULL,
    category_id INT NOT NULL,
    
    -- Составной первичный ключ защищает от дублей (нельзя привязать статью к одной категории дважды)
    PRIMARY KEY (article_id, category_id),
    
    -- Внешние ключи с каскадным удалением
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- создаем индексы для ускорения выборок, которые будут на сайте
CREATE INDEX idx_articles_created_at ON articles(created_at);
CREATE INDEX idx_articles_views ON articles(views);