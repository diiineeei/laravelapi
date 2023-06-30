# Variáveis de ambiente
CONTAINER_NAME=app

# Parar o serviço do MySQL local
stop-mysql-local:
	sudo service mysql stop

# Regra para criar um novo projeto Laravel
create-project:
	# Utiliza o comando "composer create-project" para criar o projeto Laravel
	docker exec -it $(CONTAINER_NAME) composer create-project --prefer-dist laravel/laravel $(name)

# Regra para criar um novo controller no Laravel
create-controller:
	# Utiliza o comando "php artisan make:controller" para criar o controller
	# O nome do controller é passado como argumento "name" no formato "NomeDoController"
	docker exec -it $(CONTAINER_NAME) php artisan make:controller $(name)Controller

# Comando para executar as migrações do banco de dados vinculado à aplicação
migrate:
	docker exec -it $(CONTAINER_NAME) php artisan migrate --force

# Comando para criar migrações do banco de dados vinculado à aplicação com um argumento "name" sendo o nome do arquivo
# Exemplo: make create-migration name=create_employees_table
create-migration:
	docker exec -it $(CONTAINER_NAME) php artisan make:migration $(name)

# Regra para criar um novo teste unitário no Laravel
create-unit-test:
	# Utiliza o comando "php artisan make:test" para criar o teste unitário
	# O nome do teste é passado como argumento "name" no formato "NomeDoTeste"
	docker exec -it $(CONTAINER_NAME) php artisan make:test $(name)Test --unit

# Iniciar os contêineres Docker
containers-up: stop-mysql-local
	docker-compose up --build -d

# Parar e remover os contêineres Docker
containers-down:
	docker-compose down
