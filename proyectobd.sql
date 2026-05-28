--
-- PostgreSQL database dump
--

\restrict Ku0b0bDFIp0dUGIzWZPa7iwmeWWZXgAq2Czf5HmtEResFPZPR4r7xveYe0p8rEV

-- Dumped from database version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: verificar_porcentaje_cohorte(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.verificar_porcentaje_cohorte() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    total_porcentaje NUMERIC(5,2);
BEGIN
    -- Calcular la suma de los porcentajes de los otros cohortes del mismo curso
    IF TG_OP = 'INSERT' THEN
        SELECT COALESCE(SUM(porcentaje), 0) INTO total_porcentaje
        FROM notas
        WHERE cod_cur = NEW.cod_cur;
    ELSIF TG_OP = 'UPDATE' THEN
        SELECT COALESCE(SUM(porcentaje), 0) INTO total_porcentaje
        FROM notas
        WHERE cod_cur = NEW.cod_cur AND nota <> OLD.nota;
    END IF;

    -- Sumar el porcentaje del cohorte actual (nuevo o modificado)
    total_porcentaje := total_porcentaje + NEW.porcentaje;

    -- Validar si supera el 100%
    IF total_porcentaje > 100.00 THEN
        RAISE EXCEPTION 'La suma de los porcentajes de los cohortes del curso no puede superar el 100%%. Total actual con este cambio: %', total_porcentaje;
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.verificar_porcentaje_cohorte() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: calificaciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.calificaciones (
    cod_cal integer NOT NULL,
    nota character varying(20),
    valor numeric(3,1),
    fecha date,
    cod_cur character varying(20),
    cod_est character varying(20),
    year integer,
    periodo character varying(10),
    CONSTRAINT calificaciones_valor_check CHECK (((valor >= 0.0) AND (valor <= 5.0)))
);


ALTER TABLE public.calificaciones OWNER TO postgres;

--
-- Name: calificaciones_cod_cal_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.calificaciones_cod_cal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.calificaciones_cod_cal_seq OWNER TO postgres;

--
-- Name: calificaciones_cod_cal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.calificaciones_cod_cal_seq OWNED BY public.calificaciones.cod_cal;


--
-- Name: cursos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cursos (
    cod_cur character varying(20) NOT NULL,
    nomb_cur character varying(100) NOT NULL,
    cod_doc character varying(20)
);


ALTER TABLE public.cursos OWNER TO postgres;

--
-- Name: docentes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.docentes (
    cod_doc character varying(20) NOT NULL,
    nomb_doc character varying(100) NOT NULL,
    clave character varying(50) NOT NULL
);


ALTER TABLE public.docentes OWNER TO postgres;

--
-- Name: estudiantes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.estudiantes (
    cod_est character varying(20) NOT NULL,
    nomb_est character varying(100) NOT NULL
);


ALTER TABLE public.estudiantes OWNER TO postgres;

--
-- Name: inscripciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inscripciones (
    cod_cur character varying(20) NOT NULL,
    cod_est character varying(20) NOT NULL,
    year integer NOT NULL,
    periodo character varying(10) NOT NULL,
    CONSTRAINT inscripciones_year_check CHECK ((year >= 0))
);


ALTER TABLE public.inscripciones OWNER TO postgres;

--
-- Name: notas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notas (
    nota character varying(20) NOT NULL,
    desc_nota character varying(100) NOT NULL,
    porcentaje numeric(5,2),
    posicion integer,
    cod_cur character varying(20),
    CONSTRAINT notas_porcentaje_check CHECK ((porcentaje >= (0)::numeric)),
    CONSTRAINT notas_posicion_check CHECK ((posicion >= 0))
);


ALTER TABLE public.notas OWNER TO postgres;

--
-- Name: calificaciones cod_cal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones ALTER COLUMN cod_cal SET DEFAULT nextval('public.calificaciones_cod_cal_seq'::regclass);


--
-- Data for Name: calificaciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.calificaciones (cod_cal, nota, valor, fecha, cod_cur, cod_est, year, periodo) FROM stdin;
3	N1-BD	4.3	2022-04-26	CUR-01	6100023	2022	Periodo I
4	N2-BD	2.9	2026-05-21	CUR-01	6100023	2022	Periodo II
5	N1-BD	4.0	2026-05-21	CUR-01	6100023	2022	Periodo II
6	N3-BD	3.0	2026-05-21	CUR-01	6100023	2022	Periodo II
40	N1_CUR-03_34	4.0	2026-05-21	CUR-03	6100039	2026	Periodo I
61	N1_CUR-04_12	4.0	2026-05-25	CUR-04	6100033	2023	Periodo II
62	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100033	2023	Periodo II
41	N2_CUR-03_93	3.0	2026-05-21	CUR-03	6100039	2026	Periodo I
7	N1_CUR-03_34	0.7	2026-05-21	CUR-03	6100023	2022	Periodo I
8	N2_CUR-03_93	5.0	2026-05-21	CUR-03	6100023	2022	Periodo I
10	N1_CUR-03_34	3.0	2026-05-21	CUR-03	6100041	2026	Periodo I
43	N1_CUR-03_34	2.6	2026-05-21	CUR-03	6100032	2026	Periodo I
44	N2_CUR-03_93	2.4	2026-05-21	CUR-03	6100032	2026	Periodo I
11	N2_CUR-03_93	3.0	2026-05-21	CUR-03	6100041	2026	Periodo I
46	N1_CUR-03_34	3.3	2026-05-21	CUR-03	6100044	2026	Periodo I
13	N1_CUR-03_34	5.0	2026-05-21	CUR-03	6100028	2026	Periodo I
14	N2_CUR-03_93	4.0	2026-05-21	CUR-03	6100028	2026	Periodo I
47	N2_CUR-03_93	2.7	2026-05-21	CUR-03	6100044	2026	Periodo I
16	N1_CUR-03_34	1.0	2026-05-21	CUR-03	6100033	2026	Periodo I
17	N2_CUR-03_93	5.0	2026-05-21	CUR-03	6100033	2026	Periodo I
19	N1_CUR-03_34	2.0	2026-05-21	CUR-03	6100026	2026	Periodo I
20	N2_CUR-03_93	2.3	2026-05-21	CUR-03	6100026	2026	Periodo I
52	N1_CUR-02_68	3.0	2026-05-24	CUR-02	6100028	2026	Periodo I
53	N2_CUR-02_21	4.0	2026-05-24	CUR-02	6100028	2026	Periodo I
54	N3_CUR-02_50	2.2	2026-05-24	CUR-02	6100028	2026	Periodo I
49	N1_CUR-04_12	3.1	2026-05-24	CUR-04	6100030	2026	Periodo I
22	N1_CUR-03_34	3.0	2026-05-21	CUR-03	6100035	2026	Periodo I
23	N2_CUR-03_93	2.3	2026-05-21	CUR-03	6100035	2026	Periodo I
25	N1_CUR-03_34	5.0	2026-05-21	CUR-03	6100038	2026	Periodo I
26	N2_CUR-03_93	4.5	2026-05-21	CUR-03	6100038	2026	Periodo I
28	N1_CUR-03_34	3.2	2026-05-21	CUR-03	6100042	2026	Periodo I
29	N2_CUR-03_93	4.0	2026-05-21	CUR-03	6100042	2026	Periodo I
31	N1_CUR-03_34	1.0	2026-05-21	CUR-03	6100043	2026	Periodo I
50	N2_CUR-04_80	4.7	2026-05-24	CUR-04	6100030	2026	Periodo I
32	N2_CUR-03_93	1.0	2026-05-21	CUR-03	6100043	2026	Periodo I
51	N3_CUR-04_94	1.9	2026-05-24	CUR-04	6100030	2026	Periodo I
34	N1_CUR-03_34	2.0	2026-05-21	CUR-03	6100030	2026	Periodo I
35	N2_CUR-03_93	1.0	2026-05-21	CUR-03	6100030	2026	Periodo I
37	N1_CUR-03_34	5.0	2026-05-21	CUR-03	6100027	2026	Periodo I
38	N2_CUR-03_93	2.0	2026-05-21	CUR-03	6100027	2026	Periodo I
56	N2_CUR-04_80	2.0	2026-05-25	CUR-04	6100041	2023	Periodo II
57	N3_CUR-04_94	3.0	2026-05-25	CUR-04	6100041	2023	Periodo II
58	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100028	2023	Periodo II
59	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100028	2023	Periodo II
60	N3_CUR-04_94	3.0	2026-05-25	CUR-04	6100028	2023	Periodo II
55	N1_CUR-04_12	1.0	2026-05-25	CUR-04	6100041	2023	Periodo II
63	N3_CUR-04_94	3.0	2026-05-25	CUR-04	6100033	2023	Periodo II
64	N1_CUR-04_12	4.0	2026-05-25	CUR-04	6100026	2023	Periodo II
65	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100026	2023	Periodo II
66	N3_CUR-04_94	5.0	2026-05-25	CUR-04	6100026	2023	Periodo II
67	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100035	2023	Periodo II
68	N2_CUR-04_80	4.0	2026-05-25	CUR-04	6100035	2023	Periodo II
69	N3_CUR-04_94	5.0	2026-05-25	CUR-04	6100035	2023	Periodo II
70	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100038	2023	Periodo II
71	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100038	2023	Periodo II
72	N3_CUR-04_94	4.0	2026-05-25	CUR-04	6100038	2023	Periodo II
73	N1_CUR-04_12	2.0	2026-05-25	CUR-04	6100042	2023	Periodo II
74	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100042	2023	Periodo II
75	N3_CUR-04_94	1.0	2026-05-25	CUR-04	6100042	2023	Periodo II
76	N1_CUR-04_12	1.0	2026-05-25	CUR-04	6100043	2023	Periodo II
77	N2_CUR-04_80	1.0	2026-05-25	CUR-04	6100043	2023	Periodo II
78	N3_CUR-04_94	1.0	2026-05-25	CUR-04	6100043	2023	Periodo II
79	N1_CUR-04_12	5.0	2026-05-25	CUR-04	6100023	2023	Periodo II
80	N2_CUR-04_80	2.0	2026-05-25	CUR-04	6100023	2023	Periodo II
81	N3_CUR-04_94	2.0	2026-05-25	CUR-04	6100023	2023	Periodo II
82	N1_CUR-04_12	2.0	2026-05-25	CUR-04	6100030	2023	Periodo II
83	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100030	2023	Periodo II
84	N3_CUR-04_94	2.0	2026-05-25	CUR-04	6100030	2023	Periodo II
85	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100027	2023	Periodo II
86	N2_CUR-04_80	2.0	2026-05-25	CUR-04	6100027	2023	Periodo II
87	N3_CUR-04_94	2.0	2026-05-25	CUR-04	6100027	2023	Periodo II
88	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100039	2023	Periodo II
89	N2_CUR-04_80	1.0	2026-05-25	CUR-04	6100039	2023	Periodo II
90	N3_CUR-04_94	5.0	2026-05-25	CUR-04	6100039	2023	Periodo II
91	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100032	2023	Periodo II
92	N2_CUR-04_80	4.0	2026-05-25	CUR-04	6100032	2023	Periodo II
93	N3_CUR-04_94	5.0	2026-05-25	CUR-04	6100032	2023	Periodo II
94	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100044	2023	Periodo II
95	N2_CUR-04_80	3.0	2026-05-25	CUR-04	6100044	2023	Periodo II
96	N3_CUR-04_94	2.0	2026-05-25	CUR-04	6100044	2023	Periodo II
97	N1_CUR-04_12	3.0	2026-05-25	CUR-04	6100036	2023	Periodo II
98	N2_CUR-04_80	2.0	2026-05-25	CUR-04	6100036	2023	Periodo II
99	N3_CUR-04_94	3.0	2026-05-25	CUR-04	6100036	2023	Periodo II
100	N2-BD	3.0	2026-05-25	CUR-01	6100041	2026	Periodo I
101	N1-BD	3.0	2026-05-25	CUR-01	6100041	2026	Periodo I
102	N3-BD	4.0	2026-05-25	CUR-01	6100041	2026	Periodo I
103	N2-BD	4.0	2026-05-25	CUR-01	6100028	2026	Periodo I
104	N1-BD	3.0	2026-05-25	CUR-01	6100028	2026	Periodo I
105	N3-BD	4.0	2026-05-25	CUR-01	6100028	2026	Periodo I
106	N2-BD	3.0	2026-05-25	CUR-01	6100033	2026	Periodo I
107	N1-BD	3.0	2026-05-25	CUR-01	6100033	2026	Periodo I
108	N3-BD	4.0	2026-05-25	CUR-01	6100033	2026	Periodo I
109	N2-BD	2.0	2026-05-25	CUR-01	6100026	2026	Periodo I
110	N1-BD	2.0	2026-05-25	CUR-01	6100026	2026	Periodo I
111	N3-BD	4.0	2026-05-25	CUR-01	6100026	2026	Periodo I
112	N2-BD	2.0	2026-05-25	CUR-01	6100035	2026	Periodo I
113	N1-BD	3.0	2026-05-25	CUR-01	6100035	2026	Periodo I
114	N3-BD	4.0	2026-05-25	CUR-01	6100035	2026	Periodo I
115	N2-BD	3.0	2026-05-25	CUR-01	6100038	2026	Periodo I
116	N1-BD	2.0	2026-05-25	CUR-01	6100038	2026	Periodo I
117	N3-BD	4.0	2026-05-25	CUR-01	6100038	2026	Periodo I
118	N2-BD	3.0	2026-05-25	CUR-01	6100042	2026	Periodo I
119	N1-BD	3.0	2026-05-25	CUR-01	6100042	2026	Periodo I
120	N3-BD	4.0	2026-05-25	CUR-01	6100042	2026	Periodo I
121	N2-BD	5.0	2026-05-25	CUR-01	6100043	2026	Periodo I
122	N1-BD	1.0	2026-05-25	CUR-01	6100043	2026	Periodo I
123	N3-BD	3.0	2026-05-25	CUR-01	6100043	2026	Periodo I
124	N2-BD	5.0	2026-05-25	CUR-01	6100030	2026	Periodo I
125	N1-BD	2.0	2026-05-25	CUR-01	6100030	2026	Periodo I
126	N3-BD	2.0	2026-05-25	CUR-01	6100030	2026	Periodo I
127	N2-BD	5.0	2026-05-25	CUR-01	6100027	2026	Periodo I
128	N1-BD	3.0	2026-05-25	CUR-01	6100027	2026	Periodo I
129	N3-BD	3.0	2026-05-25	CUR-01	6100027	2026	Periodo I
130	N2-BD	5.0	2026-05-25	CUR-01	6100039	2026	Periodo I
131	N1-BD	4.0	2026-05-25	CUR-01	6100039	2026	Periodo I
132	N3-BD	3.0	2026-05-25	CUR-01	6100039	2026	Periodo I
133	N2-BD	5.0	2026-05-25	CUR-01	6100032	2026	Periodo I
134	N1-BD	3.0	2026-05-25	CUR-01	6100032	2026	Periodo I
135	N3-BD	3.0	2026-05-25	CUR-01	6100032	2026	Periodo I
136	N2-BD	5.0	2026-05-25	CUR-01	6100044	2026	Periodo I
137	N1-BD	2.0	2026-05-25	CUR-01	6100044	2026	Periodo I
138	N3-BD	3.0	2026-05-25	CUR-01	6100044	2026	Periodo I
139	N2-BD	5.0	2026-05-25	CUR-01	6100036	2026	Periodo I
140	N1-BD	1.0	2026-05-25	CUR-01	6100036	2026	Periodo I
141	N3-BD	2.0	2026-05-25	CUR-01	6100036	2026	Periodo I
142	N2-BD	5.0	2026-05-25	CUR-01	6100037	2026	Periodo I
143	N1-BD	2.0	2026-05-25	CUR-01	6100037	2026	Periodo I
144	N3-BD	1.0	2026-05-25	CUR-01	6100037	2026	Periodo I
145	N2-BD	5.0	2026-05-25	CUR-01	6100040	2026	Periodo I
146	N1-BD	3.0	2026-05-25	CUR-01	6100040	2026	Periodo I
147	N3-BD	2.0	2026-05-25	CUR-01	6100040	2026	Periodo I
148	N2-BD	4.0	2026-05-25	CUR-01	6100034	2026	Periodo I
149	N1-BD	4.0	2026-05-25	CUR-01	6100034	2026	Periodo I
150	N3-BD	3.0	2026-05-25	CUR-01	6100034	2026	Periodo I
151	N2-BD	4.0	2026-05-25	CUR-01	6100029	2026	Periodo I
152	N1-BD	3.0	2026-05-25	CUR-01	6100029	2026	Periodo I
153	N3-BD	4.0	2026-05-25	CUR-01	6100029	2026	Periodo I
154	N2-BD	4.0	2026-05-25	CUR-01	6100031	2026	Periodo I
155	N1-BD	3.0	2026-05-25	CUR-01	6100031	2026	Periodo I
156	N3-BD	5.0	2026-05-25	CUR-01	6100031	2026	Periodo I
157	N1_CUR-03_34	4.0	2026-05-25	CUR-03	6100028	2025	Periodo I
158	N2_CUR-03_93	3.0	2026-05-25	CUR-03	6100028	2025	Periodo I
159	N3_CUR-03_16	4.0	2026-05-25	CUR-03	6100028	2025	Periodo I
160	N4_CUR-03_67	1.0	2026-05-25	CUR-03	6100028	2025	Periodo I
\.


--
-- Data for Name: cursos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cursos (cod_cur, nomb_cur, cod_doc) FROM stdin;
CUR-01	Base de Datos	DOC-01
CUR-02	Sistemas Operativos	DOC-01
CUR-03	Estadística y Probabilidad	DOC-01
CUR-04	Ecucaciones DIferenciales  y Modelado Matemático	DOC-02
\.


--
-- Data for Name: docentes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.docentes (cod_doc, nomb_doc, clave) FROM stdin;
DOC-01	Jesús Reyes Carvajal	12345
DOC-02	Fernando Riveros	123
\.


--
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estudiantes (cod_est, nomb_est) FROM stdin;
6100023	Juan Carlos Gomez
6100024	Pilar Marquez
6100026	Carlos Perez
6100027	Laura Martinez
6100028	Andres Ramirez
6100029	Sofia Herrera
6100030	Julian Castro
6100031	Valentina Rojas
6100032	Mateo Vargas
6100033	Camila Torres
6100034	Sebastian Ruiz
6100035	Daniela Moreno
6100036	Nicolas Silva
6100037	Paula Mendoza
6100038	David Lopez
6100039	Mariana Jimenez
6100040	Santiago Ortega
6100041	Ana Rodriguez
6100042	Felipe Navarro
6100043	Isabella Cruz
6100044	Miguel Santos
\.


--
-- Data for Name: inscripciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inscripciones (cod_cur, cod_est, year, periodo) FROM stdin;
CUR-01	6100023	2022	Periodo I
CUR-01	6100024	2022	Periodo I
CUR-01	6100023	2022	Periodo II
CUR-03	6100023	2022	Periodo I
CUR-03	6100026	2026	Periodo I
CUR-03	6100027	2026	Periodo I
CUR-03	6100028	2026	Periodo I
CUR-03	6100029	2026	Periodo I
CUR-03	6100030	2026	Periodo I
CUR-03	6100031	2026	Periodo I
CUR-03	6100032	2026	Periodo I
CUR-03	6100033	2026	Periodo I
CUR-03	6100034	2026	Periodo I
CUR-03	6100035	2026	Periodo I
CUR-03	6100036	2026	Periodo I
CUR-03	6100037	2026	Periodo I
CUR-03	6100038	2026	Periodo I
CUR-03	6100039	2026	Periodo I
CUR-03	6100040	2026	Periodo I
CUR-03	6100041	2026	Periodo I
CUR-03	6100042	2026	Periodo I
CUR-03	6100043	2026	Periodo I
CUR-03	6100044	2026	Periodo I
CUR-02	6100039	2026	Periodo I
CUR-02	6100028	2026	Periodo I
CUR-02	6100031	2026	Periodo I
CUR-02	6100024	2026	Periodo I
CUR-02	6100030	2026	Periodo I
CUR-04	6100030	2026	Periodo I
CUR-04	6100033	2022	Periodo I
CUR-04	6100030	2022	Periodo I
CUR-02	6100033	2025	Periodo I
CUR-03	6100028	2025	Periodo II
CUR-03	6100041	2025	Periodo II
CUR-03	6100044	2025	Periodo II
CUR-02	6100037	2026	Periodo I
CUR-04	6100026	2023	Periodo II
CUR-04	6100027	2023	Periodo II
CUR-04	6100028	2023	Periodo II
CUR-04	6100029	2023	Periodo II
CUR-04	6100030	2023	Periodo II
CUR-04	6100031	2023	Periodo II
CUR-04	6100032	2023	Periodo II
CUR-04	6100033	2023	Periodo II
CUR-04	6100034	2023	Periodo II
CUR-04	6100035	2023	Periodo II
CUR-04	6100036	2023	Periodo II
CUR-04	6100037	2023	Periodo II
CUR-04	6100038	2023	Periodo II
CUR-04	6100039	2023	Periodo II
CUR-04	6100040	2023	Periodo II
CUR-04	6100041	2023	Periodo II
CUR-04	6100042	2023	Periodo II
CUR-04	6100043	2023	Periodo II
CUR-04	6100044	2023	Periodo II
CUR-04	6100023	2023	Periodo II
CUR-04	6100024	2023	Periodo II
CUR-01	6100041	2026	Periodo I
CUR-01	6100026	2026	Periodo I
CUR-01	6100027	2026	Periodo I
CUR-01	6100028	2026	Periodo I
CUR-01	6100029	2026	Periodo I
CUR-01	6100030	2026	Periodo I
CUR-01	6100031	2026	Periodo I
CUR-01	6100032	2026	Periodo I
CUR-01	6100033	2026	Periodo I
CUR-01	6100034	2026	Periodo I
CUR-01	6100035	2026	Periodo I
CUR-01	6100036	2026	Periodo I
CUR-01	6100037	2026	Periodo I
CUR-01	6100038	2026	Periodo I
CUR-01	6100039	2026	Periodo I
CUR-01	6100040	2026	Periodo I
CUR-01	6100042	2026	Periodo I
CUR-01	6100043	2026	Periodo I
CUR-01	6100044	2026	Periodo I
CUR-03	6100028	2025	Periodo I
\.


--
-- Data for Name: notas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notas (nota, desc_nota, porcentaje, posicion, cod_cur) FROM stdin;
N2-BD	Parcial dos	30.00	2	CUR-01
N3-BD	Examen final	40.00	3	CUR-01
N1_CUR-03_34	Primer corte	40.00	1	CUR-03
N2_CUR-03_93	Segundo corte	30.00	2	CUR-03
N1_CUR-04_12	Primer cohorte	20.00	1	CUR-04
N2_CUR-04_80	Segundo cohorte	30.00	2	CUR-04
N3_CUR-04_94	Tercer cohorte	50.00	3	CUR-04
N1_CUR-02_68	e	30.00	1	CUR-02
N2_CUR-02_21	2	30.00	2	CUR-02
N3_CUR-02_50	3	40.00	3	CUR-02
N3_CUR-03_16	Tercer cohorte	15.00	3	CUR-03
N4_CUR-03_67	cuarto cohorte	15.00	4	CUR-03
N1-BD	Parcial uno	30.00	2	CUR-01
\.


--
-- Name: calificaciones_cod_cal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.calificaciones_cod_cal_seq', 160, true);


--
-- Name: calificaciones calificaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT calificaciones_pkey PRIMARY KEY (cod_cal);


--
-- Name: cursos cursos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cursos
    ADD CONSTRAINT cursos_pkey PRIMARY KEY (cod_cur);


--
-- Name: docentes docentes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.docentes
    ADD CONSTRAINT docentes_pkey PRIMARY KEY (cod_doc);


--
-- Name: estudiantes estudiantes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_pkey PRIMARY KEY (cod_est);


--
-- Name: inscripciones inscripciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT inscripciones_pkey PRIMARY KEY (cod_cur, cod_est, year, periodo);


--
-- Name: notas notas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notas
    ADD CONSTRAINT notas_pkey PRIMARY KEY (nota);


--
-- Name: notas trigger_verificar_porcentaje_cohorte; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_verificar_porcentaje_cohorte BEFORE INSERT OR UPDATE ON public.notas FOR EACH ROW EXECUTE FUNCTION public.verificar_porcentaje_cohorte();


--
-- Name: calificaciones calificaciones_cod_cur_cod_est_year_periodo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT calificaciones_cod_cur_cod_est_year_periodo_fkey FOREIGN KEY (cod_cur, cod_est, year, periodo) REFERENCES public.inscripciones(cod_cur, cod_est, year, periodo) ON DELETE CASCADE;


--
-- Name: calificaciones calificaciones_nota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT calificaciones_nota_fkey FOREIGN KEY (nota) REFERENCES public.notas(nota) ON DELETE CASCADE;


--
-- Name: cursos cursos_cod_doc_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cursos
    ADD CONSTRAINT cursos_cod_doc_fkey FOREIGN KEY (cod_doc) REFERENCES public.docentes(cod_doc);


--
-- Name: inscripciones inscripciones_cod_cur_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT inscripciones_cod_cur_fkey FOREIGN KEY (cod_cur) REFERENCES public.cursos(cod_cur) ON DELETE CASCADE;


--
-- Name: inscripciones inscripciones_cod_est_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT inscripciones_cod_est_fkey FOREIGN KEY (cod_est) REFERENCES public.estudiantes(cod_est) ON DELETE CASCADE;


--
-- Name: notas notas_cod_cur_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notas
    ADD CONSTRAINT notas_cod_cur_fkey FOREIGN KEY (cod_cur) REFERENCES public.cursos(cod_cur) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict Ku0b0bDFIp0dUGIzWZPa7iwmeWWZXgAq2Czf5HmtEResFPZPR4r7xveYe0p8rEV

