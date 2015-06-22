** Introducción ** 

OCR2DATA esta diseñado para integrarse en procesos existentes, pero puede funcionar de manera independiente.

** Gestión **

OCR2DATA utiliza pero no llena las tablas 
 data_clientes
 data_direccionescliente
 data_productos
 
Estas tablas deben llenarse desde otros procesos con los nombres de clientes, direcciones y productos de nuestro negocio. La deteccion inteligente funciona buscando los clientes, direcciones o productos mas parecidos a los que tenemos en la base de datos, aproximando lo que ha detectado el OCR. Esto permite en algun caso devolver datos limpios e id's de base de datos. producto_id 813 en lugar de a lo mejor "Mesa de R0ble". Idealmente estas tablas tendran todos los productos y clientes, y serian actualizadas periodicamente por otro proceso.

El proceso completo que todo documento sigue es el siguiente:
 - Importacion desde el exterior utilizando un fichero .ini con sus datos
 - Creación de una nueva template si no se autodetecta una existente util para este documento
 - Configuracion de este template, si es nueva y no ha sido configurada antes
 - Correr el proceso "Procesar" que procesara el siguiente documento listo para OCR
 - Revision visual/arreglo por operador y marcado como "tramitado", "pendiente",...
 - Tras marcar un documento como "tramitado", "pendiente", al operador se le presenta el siguiente documento pendiente, y asi otro y otro hasta que ha revisado todos.
 - Correr el proceso "Exportar" que procesara el siguiente documento listo para Exportacion, utilizando el plugin de exportacion que haya instalado (por defecto .ini). Esto creara (dependiendo del plugin) un documento electronico con los datos detectados y revisados por el operador.

Normalmente, se automatizara la importación, procesado y exportación de documentos, invocando directamente sus scripts. De este modo los operadores realizan una sola tarea, revisar el trabajo de OCR y hacer correcciones cuando sea necesaria. El flujo de trabajo de los operadores esta optimizado para ser lo mas rapido posible, agil, y que lo pueden desempeñar en muchos casos utilizando solo el teclado.

** Gestión automatica **

Invocacion programada del importador de documentos:

Script: importar.php
Parametros:
 - templatename Nombre de template (se utiliza si no autodetecta una template existente)
 - id_comm Identificador en la plataforma externa (numerico)
 - path Path absoluto al fichero ini con los detalles de importacion. 
 
 De este fichero .ini se dan dos ejemplo, tienen al menos 3 campos obligatorios
 origen una cadena de texto representando el origen del documento, sea fax, correo electronico, etc. Se utilizar para la autodeteccion de templates.
 imagen el fichero de origen, puede ser pdf, tiff o jpg. Generalmente sera formato pdf.
 imagentipo indica el formato del fichero anterior. 
 Se recomienda utilizar uno de estos tres formatos, pdf, tiff o jpg. Aunque se utiliza para la conversion interna de formatos ImageMagick, que soporta cientos de formatos. Asi que un formato distinto podria funcionar. Sino, habria que hacer una conversion intermedia que se recomienda matenga la misma calidad grafica, para no perder detalles que podrian perjudicar el OCR. PDF es el formato ideal, y el que permite mejor calidad de OCR. 
 
Script: proceso.php
No utiliza parametros.
Toma el siguiente documento con template correctamente configurada, y realiza el OCR de deteccion por zonas, depositando la informacion detectada en la base de datos de OCR2DATA.
Solo procesa un solo documento, asi que debe ser invocado varias veces. El OCR es intensivo en CPU e I/O. Lo ideal seria invocarlo mediante un cron cada X minutos, un poco mas rapido que la velocidad a la que lleguen documentos.

El plugin de exportacion ini utiliza el script proceso.php para su invocacion. Al finalizar proceso.php se habra creado un fichero de exportacion en el fichero de salida con los datos detectados. 

 

 





