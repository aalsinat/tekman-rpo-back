## Prueba final Tekman (Back)

Para el desarrollo de la parte back de la prueba, y dado mi desconocimiento actual en lo que a frameworks de PHP se refiere, ha sido un poco más complicado.
He decidido utilizar el framework **Lumen**, porque es un poco más ligero que **Laravel**.

La solución planteada se basa principalmente en el desarrollo de un servicio `TwitterService` para dar respuesta a las peticiones relacionadas con la API de Twitter.
Este servicio se apoya en un proxy `TwitterProxy`, que facilita la interacción con la REST API. Este proxy se ha desarrollado sin dependencias de librerias del framework, 
para mantenerlo más desacoplado.
En cuanto al servicio `TwitterService`, el framework lo **inyecta por dependencias** al controlador `TwitterController` que da respuesta al único endpoint expuesto: 
- `/api/user/:screen_name/last-tweets`

Para conseguir esta inyección de dependencias se ha desarrollado el _Provider_ `TwitterServiceProvider` que es el que registra el servicio en el contenedor.

Se ha activado la cache a través de fichero para mejorar el tiempo de respuesta de las peticiones.

Finalmente, se ha tenido que modificar el fichero `routes/web.php` para exponer el único endpoint de la API desarrollada.

## Deuda técnica

### Test unitarios
No he podido más que empezar a escribir un test unitario.

### Arquitectura y paradigmas
En cuanto a la aplicación de paradigmas como Hexagonal Architecture, me ha sido imposible teniendo en cuenta que apenas he tenido tiempo de aprender este framework. 
La arquitectura Hexagonal define capas conceptuales de responsabilidad de código, y define el modo en que debe desacoplarse el código entre esas capas.
Expone temas comunes como desacoplamiento del código del framework utilizado, de modo que nuestra aplicación sólo utiliza dicho framework para cumplir sus tareas.
El framework nunca debe ser nuestra aplicación. Los objetivos principales cuando se habla de arquitectura son básicamente: mantenibilidad y deuda técnica.

##### Mantenibilidad
En el caso de este desarrollo, como he comentado, la solución planteada con el uso de un servicio que se apoya en un Proxy para hacer llamadas a la API.
En este punto existen múltiples puntos de mejora para evitar que tener que modificar esta clase cada vez que se quiere acceder a una nueva funcionalidad de la API de twitter.
Se podria plantear el modelado del concepto `Recurso`, entendido como una URL, los métodos que acepta y también sus parámetros. De este modo, cada vez que se quiere incorporar
un nuevo recurso al proxy, se crea una nueva clase `Recurso` y se registra en el proxy como un servicio o funcionalidad más. Esto nos permitiria tener distintos equipoas añadiendo 
nuevas funcionalidades sin necessidad de tener que trabajar en la misma clase.

Un punto interesante para poder desacoplar más el desarrollo del framework, seria plantearlo como una libreria externa, por ejemplo en el carpeta `tekman`, que se cargaría automáticamente mediante `autoload` o bien 
a través de la opción de composer:

```
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        },
        "classmap": [
            "tekman/"
        ]
    }
``` 
Esta carpeta contendrías los ficheros `TwitterProxy.php` y `TwitterService.php`, completamente funcionales y desvinculadas de *Lumen*.