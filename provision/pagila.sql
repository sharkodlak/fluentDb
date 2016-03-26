ALTER TABLE category
	ADD UNIQUE (name);
ALTER TABLE film
	ADD UNIQUE (title);
INSERT INTO film (film_id, title, description, language_id, length, release_year) VALUES
	(-1, 'Die Welle', 'A high school teacher''s experiment to demonstrate to his students what life is like under a dictatorship spins horribly out of control when he forms a social unit with a life of its own.', (SELECT language_id FROM language WHERE name = 'German'), 110, 2008);
INSERT INTO category (category_id, name) VALUES
	(-1, 'Thriller');
INSERT INTO film_category (film_id, category_id) VALUES
	((SELECT film_id FROM film WHERE title = 'Die Welle'), (SELECT category_id FROM category WHERE name = 'Drama')),
	((SELECT film_id FROM film WHERE title = 'Die Welle'), (SELECT category_id FROM category WHERE name = 'Thriller'));
