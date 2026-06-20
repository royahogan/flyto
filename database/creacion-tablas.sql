USE flyto;

CREATE TABLE tipos_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    
    CONSTRAINT uq_tipos_usuarios_nombre UNIQUE (nombre)
);

CREATE TABLE estados_reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    
    CONSTRAINT uq_estados_reservas_nombre UNIQUE (nombre)
);

CREATE TABLE estados_promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    
    CONSTRAINT uq_estados_promociones_nombre UNIQUE (nombre)
);

CREATE TABLE estados_vuelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    
    CONSTRAINT uq_estados_vuelos_nombre UNIQUE (nombre)
);

CREATE TABLE paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    codigo CHAR(3) NOT NULL,

    CONSTRAINT uq_paises_nombre UNIQUE (nombre),
    CONSTRAINT uq_paises_codigo UNIQUE (codigo)
);

CREATE TABLE ciudades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    abreviacion CHAR(3) NOT NULL,
    pais_id INT NOT NULL,

    CONSTRAINT fk_ciudades_pais
        FOREIGN KEY (pais_id) REFERENCES paises(id),

    CONSTRAINT uq_ciudad_pais UNIQUE (nombre, pais_id),

    CONSTRAINT uq_ciudades_abreviacion UNIQUE (abreviacion)
);

CREATE TABLE novedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    texto VARCHAR(200) NOT NULL,
    fechaPublicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaExpiracion DATETIME NOT NULL,
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefono VARCHAR(20) NULL,
    clave_hash VARCHAR(255) NOT NULL,
    tipo_usuario_id INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT FALSE,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    email_verificado BOOLEAN NOT NULL DEFAULT FALSE,
    token_verificacion VARCHAR(255) NULL,
    token_recupero VARCHAR(255) NULL,
    token_expiracion DATETIME NULL,

    CONSTRAINT uq_usuarios_email UNIQUE (email),

    CONSTRAINT fk_usuarios_tipo
        FOREIGN KEY (tipo_usuario_id) REFERENCES tipos_usuarios(id)
);

CREATE TABLE aerolineas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    codigo_iata CHAR(3) NOT NULL,
    pais_id INT NOT NULL,
    ceo_id INT NULL,
    activa BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT uq_aerolineas_nombre UNIQUE (nombre),
    CONSTRAINT uq_aerolineas_codigo UNIQUE (codigo_iata),

    CONSTRAINT fk_aerolineas_pais
        FOREIGN KEY (pais_id) REFERENCES paises(id),

    CONSTRAINT fk_aerolineas_ceo
        FOREIGN KEY (ceo_id) REFERENCES usuarios(id)
);

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(200) NOT NULL,
    descuento DECIMAL(4,3) NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion DATETIME NULL,
    fecha_fin DATETIME NULL,
    estado_id INT NOT NULL,
    aerolinea_id INT NOT NULL,
    activa BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT fk_promociones_estado
        FOREIGN KEY (estado_id) REFERENCES estados_promociones(id),

    CONSTRAINT fk_promociones_aerolinea
        FOREIGN KEY (aerolinea_id) REFERENCES aerolineas(id)
);

CREATE TABLE vuelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigoVuelo VARCHAR(10) NOT NULL,
    aerolinea_id INT NOT NULL,
    origen_ciudad_id INT NOT NULL,
    destino_ciudad_id INT NOT NULL,

    precio DECIMAL(10,2) NOT NULL,
    asientos_disponibles INT NOT NULL,
    asientosOcupados INT NOT NULL DEFAULT 0,

    fecha_salida DATETIME NOT NULL,
    fecha_llegada DATETIME NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    distancia_km INT NOT NULL,
    duracion_horas DECIMAL(5,2) NOT NULL,

    estado_id INT NOT NULL,

    CONSTRAINT uq_vuelos_codigoVuelo UNIQUE (codigoVuelo),

    CONSTRAINT fk_vuelos_aerolinea
        FOREIGN KEY (aerolinea_id) REFERENCES aerolineas(id),

    CONSTRAINT fk_vuelos_origen
        FOREIGN KEY (origen_ciudad_id) REFERENCES ciudades(id),

    CONSTRAINT fk_vuelos_destino
        FOREIGN KEY (destino_ciudad_id) REFERENCES ciudades(id),

    CONSTRAINT fk_vuelos_estado
        FOREIGN KEY (estado_id) REFERENCES estados_vuelos(id)
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    vuelo_id INT NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,

    fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    estado_id INT NOT NULL,

    CONSTRAINT fk_reservas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),

    CONSTRAINT fk_reservas_vuelo
        FOREIGN KEY (vuelo_id) REFERENCES vuelos(id),

    CONSTRAINT fk_reservas_estado
        FOREIGN KEY (estado_id) REFERENCES estados_reservas(id)
);

CREATE TABLE pasajeros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,

    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    documento VARCHAR(30) NOT NULL,
    pasaporte VARCHAR(30) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono_contacto VARCHAR(30) NOT NULL,
    correo_electronico VARCHAR(120) NOT NULL,

    CONSTRAINT fk_pasajeros_reserva
        FOREIGN KEY (reserva_id) REFERENCES reservas(id)
        ON DELETE CASCADE
);

CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,

    nombre_titular VARCHAR(120) NOT NULL,
    ultimos_cuatro_digitos CHAR(4) NOT NULL,
    vencimiento_mes TINYINT NOT NULL,
    vencimiento_anio SMALLINT NOT NULL,

    fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_metodos_pago_reserva
        UNIQUE (reserva_id),

    CONSTRAINT fk_metodos_pago_reserva
        FOREIGN KEY (reserva_id) REFERENCES reservas(id)
        ON DELETE CASCADE
);