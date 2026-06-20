USE flyto;

INSERT INTO tipos_usuarios 
    (nombre) 
VALUES
    ('administrador'),
    ('ceo'),
    ('cliente');

INSERT INTO estados_reservas (nombre)
VALUES
    ('confirmada'),
    ('cancelada'),
    ('completada');

INSERT INTO novedades (
    titulo,
    categoria,
    texto,
    fechaPublicacion,
    fechaExpiracion
) VALUES
    (
        'Mantenimiento programado',
        'Sistema',
        'El sitio permanecerá en mantenimiento el domingo de 02:00 a 05:00.',
        '2026-06-01 09:00:00',
        '2026-06-16 23:59:59'
    ),
    (
        'Demoras por clima',
        'Vuelos',
        'Algunos vuelos pueden presentar demoras por condiciones climáticas adversas.',
        '2026-06-10 08:30:00',
        '2026-06-20 23:59:59'
    ),
    (
        'Nuevo destino disponible',
        'Destinos',
        'Ya se encuentra disponible la búsqueda de vuelos hacia Mendoza.',
        '2026-06-12 10:00:00',
        '2026-07-12 23:59:59'
    ),
    (
        'Actualización de políticas',
        'Reservas',
        'Se actualizaron las condiciones para cancelar reservas desde el perfil del usuario.',
        '2026-06-05 14:15:00',
        '2026-08-05 23:59:59'
    ),
    (
        'Cierre temporal de aeropuerto',
        'Aeropuertos',
        'El aeropuerto permanecerá cerrado por obras durante la madrugada del martes.',
        '2026-05-25 12:00:00',
        '2026-06-05 23:59:59'
    ),
    (
        'Atención al cliente extendida',
        'Soporte',
        'El horario de atención al cliente se extenderá hasta las 22:00 durante esta semana.',
        '2026-06-14 09:45:00',
        '2026-06-21 23:59:59'
    ),
    (
        'Reprogramación de vuelos',
        'Vuelos',
        'Los pasajeros con vuelos afectados recibirán información actualizada por correo.',
        '2026-06-13 18:00:00',
        '2026-06-18 23:59:59'
    ),
    (
        'Mejoras en el buscador',
        'Sistema',
        'Se incorporaron mejoras en los filtros de búsqueda por origen, destino y fecha.',
        '2026-06-15 11:20:00',
        '2026-09-15 23:59:59'
    ),
    (
        'Recordatorio de documentación',
        'Usuarios',
        'Recordá verificar que tus datos personales estén actualizados antes de viajar.',
        '2026-06-01 07:00:00',
        '2026-12-31 23:59:59'
    ),
    (
        'Novedad expirada de prueba',
        'Pruebas',
        'Esta novedad permite validar que no se muestren anuncios vencidos.',
        '2026-04-01 10:00:00',
        '2026-04-30 23:59:59'
    );

INSERT INTO paises (nombre, codigo) VALUES
    ('Argentina', 'ARG'),
    ('Brasil', 'BRA'),
    ('Chile', 'CHL'),
    ('Uruguay', 'URY'),
    ('Paraguay', 'PRY'),
    ('Perú', 'PER');

INSERT INTO ciudades (nombre, abreviacion, pais_id) VALUES
    ('Buenos Aires', 'BUE', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Rosario', 'ROS', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Córdoba', 'COR', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Mendoza', 'MDZ', (SELECT id FROM paises WHERE codigo = 'ARG')),

    ('São Paulo', 'SAO', (SELECT id FROM paises WHERE codigo = 'BRA')),
    ('Río de Janeiro', 'RIO', (SELECT id FROM paises WHERE codigo = 'BRA')),
    ('Brasilia', 'BSB', (SELECT id FROM paises WHERE codigo = 'BRA')),

    ('Santiago de Chile', 'SCL', (SELECT id FROM paises WHERE codigo = 'CHL')),
    ('Valparaíso', 'VAP', (SELECT id FROM paises WHERE codigo = 'CHL')),

    ('Montevideo', 'MVD', (SELECT id FROM paises WHERE codigo = 'URY')),
    ('Punta del Este', 'PDP', (SELECT id FROM paises WHERE codigo = 'URY')),

    ('Asunción', 'ASU', (SELECT id FROM paises WHERE codigo = 'PRY')),
    ('Ciudad del Este', 'CDE', (SELECT id FROM paises WHERE codigo = 'PRY')),

    ('Lima', 'LIM', (SELECT id FROM paises WHERE codigo = 'PER')),
    ('Cusco', 'CUZ', (SELECT id FROM paises WHERE codigo = 'PER')),
    ('Arequipa', 'AQP', (SELECT id FROM paises WHERE codigo = 'PER'));

INSERT INTO estados_vuelos (nombre)
VALUES
    ('pendiente'),
    ('completado'),
    ('cancelado');

INSERT INTO estados_promociones (nombre)
VALUES
    ('activa'),
    ('inactiva');

INSERT INTO usuarios (
    nombre,
    apellido,
    email,
    telefono,
    clave_hash,
    tipo_usuario_id,
    activo,
    email_verificado
)
VALUES
(
    'CEO',
    'Andes',
    'ceo.andes@flyto.com',
    '3415551001',
    SHA2('TestPass123_', 256),
    (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
    TRUE,
    TRUE
),
(
    'CEO',
    'Sur',
    'ceo.sur@flyto.com',
    '3415551002',
    SHA2('TestPass123_', 256),
    (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
    TRUE,
    TRUE
);

INSERT INTO aerolineas (
    nombre,
    descripcion,
    codigo_iata,
    pais_id,
    ceo_id,
    activa
)
VALUES
(
    'Andes Air',
    'Aerolínea argentina con vuelos nacionales e internacionales.',
    'AND',
    (SELECT id FROM paises WHERE codigo = 'ARG'),
    (SELECT id FROM usuarios WHERE email = 'ceo.andes@flyto.com'),
    TRUE
),
(
    'Sur Líneas Aéreas',
    'Aerolínea regional enfocada en destinos turísticos del sur.',
    'SUR',
    (SELECT id FROM paises WHERE codigo = 'ARG'),
    (SELECT id FROM usuarios WHERE email = 'ceo.sur@flyto.com'),
    TRUE
);

INSERT INTO promociones (
    descripcion,
    descuento,
    fecha_creacion,
    fecha_aprobacion,
    fecha_fin,
    estado_id,
    aerolinea_id,
    activa
)
VALUES
(
    '20% de descuento en vuelos nacionales seleccionados.',
    0.200,
    '2026-06-18 10:00:00',
    '2026-06-18 11:00:00',
    '2026-12-31 23:59:59',
    (SELECT id FROM estados_promociones WHERE nombre = 'activa'),
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    TRUE
);

INSERT INTO vuelos (
    codigoVuelo,
    aerolinea_id,
    origen_ciudad_id,
    destino_ciudad_id,
    precio,
    asientos_disponibles,
    asientosOcupados,
    fecha_salida,
    fecha_llegada,
    distancia_km,
    duracion_horas,
    estado_id
)
VALUES
(
    'AND001',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    (SELECT id FROM ciudades WHERE abreviacion = 'MDZ'),
    125000.00,
    120,
    0,
    '2026-07-05 08:00:00',
    '2026-07-05 10:00:00',
    985,
    2.00,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'AND002',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    (SELECT id FROM ciudades WHERE abreviacion = 'MDZ'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    118000.00,
    95,
    25,
    '2026-07-12 18:30:00',
    '2026-07-12 20:30:00',
    985,
    2.00,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'AND003',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    (SELECT id FROM ciudades WHERE abreviacion = 'ROS'),
    (SELECT id FROM ciudades WHERE abreviacion = 'COR'),
    76000.00,
    80,
    10,
    '2026-07-20 09:15:00',
    '2026-07-20 10:25:00',
    400,
    1.17,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'AND004',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
    210000.00,
    100,
    40,
    '2026-08-01 07:45:00',
    '2026-08-01 10:00:00',
    1140,
    2.25,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'AND005',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'AND'),
    (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    205000.00,
    0,
    150,
    '2026-06-10 13:00:00',
    '2026-06-10 15:15:00',
    1140,
    2.25,
    (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
),
(
    'SUR001',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'SUR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
    98000.00,
    110,
    5,
    '2026-07-08 11:00:00',
    '2026-07-08 12:00:00',
    220,
    1.00,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'SUR002',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'SUR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
    (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
    97000.00,
    105,
    15,
    '2026-07-15 16:20:00',
    '2026-07-15 17:20:00',
    220,
    1.00,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'SUR003',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'SUR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'COR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'SAO'),
    260000.00,
    90,
    30,
    '2026-08-05 06:30:00',
    '2026-08-05 09:40:00',
    1950,
    3.17,
    (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
),
(
    'SUR004',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'SUR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'SAO'),
    (SELECT id FROM ciudades WHERE abreviacion = 'COR'),
    255000.00,
    0,
    140,
    '2026-06-12 20:00:00',
    '2026-06-12 23:10:00',
    1950,
    3.17,
    (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
),
(
    'SUR005',
    (SELECT id FROM aerolineas WHERE codigo_iata = 'SUR'),
    (SELECT id FROM ciudades WHERE abreviacion = 'ROS'),
    (SELECT id FROM ciudades WHERE abreviacion = 'PDP'),
    145000.00,
    130,
    0,
    '2026-07-25 14:10:00',
    '2026-07-25 15:40:00',
    620,
    1.50,
    (SELECT id FROM estados_vuelos WHERE nombre = 'cancelado')
);