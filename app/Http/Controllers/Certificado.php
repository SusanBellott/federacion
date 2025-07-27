<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Imagencertificado;
use App\Models\Curso as ModelCurso;

class Certificado extends Controller
{
    function __construct()
    {
        $this->middleware('permission:certificados.index', ['only' => ['index']]);
        $this->middleware('permission:estuditarcursocerti.update', ['only' => ['update']]);
        $this->middleware('permission:estuditarimagencertificado.update', ['only' => ['update2']]);
        $this->middleware('permission:imagencertificadodelete.delete', ['only' => ['update3']]);
    }
    /**
     * Muestra la página del certificado para un curso específico.
     *
     * @param  string  $uuid_curso El UUID único del curso para el cual se mostrará el certificado.
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function index($uuid_curso, Request $request)
    {
        // Obtiene el valor del parámetro 'from' de la cadena de consulta (query string) de la URL.
        $from = request()->query('from');
        // Decodifica la URL base64 que se pasó como parámetro 'from'.
        $decodedUrl = base64_decode($from);
        // Busca un curso en la base de datos utilizando el UUID proporcionado.
        // - 'uuid_curso', (string) $uuid_curso: Compara la columna 'uuid_curso' con el valor del parámetro, asegurándose de que sea una cadena.
        // - 'estado', 'activo': Asegura que solo se recuperen los cursos que estén marcados como 'activo'.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ningún curso, lanza una excepción NotFoundHttpException (error 404).
        // - makeHidden(['id_curso']): Oculta el atributo 'id_curso' en el objeto del curso antes de pasarlo a la vista Inertia.
        $curso = ModelCurso::where('uuid_curso', (string) $uuid_curso)
            ->where('estado', 'activo')
            ->firstOrFail()
            ->makeHidden(['id_curso']);
        // Inicializa arrays vacíos para almacenar las imágenes del certificado.
        $imagenes = [];
        $imagenes2 = [];
        // Verifica si se encontró el curso.
        if ($curso) {
            // Obtiene todas las imágenes de certificado que no estén marcadas como 'eliminado'
            // y que estén asociadas al ID del curso encontrado.
            $imagenes2 = Imagencertificado::where('estado', '<>', 'eliminado')
                ->where('id_curso', $curso->id_curso) // Compara con el valor de uuid_curso
                ->get();
            // Obtiene solo las imágenes de certificado que estén marcadas como 'activo'
            // y que estén asociadas al ID del curso encontrado.
            $imagenes = Imagencertificado::where('estado', 'activo')
                ->where('id_curso', $curso->id_curso) // Compara con el valor de id_curso
                ->get();
        }
        // Renderiza el componente de Inertia llamado 'Certificado' y le pasa los siguientes datos como props:
        return Inertia::render('Certificado', [
            'uuid_curso' => $uuid_curso, // El UUID del curso.
            'rutaanterior' => $decodedUrl, // La URL anterior decodificada.
            'imagenes' => $imagenes, // Las imágenes de certificado activas para el curso.
            'imagenes2' => $imagenes2, // Todas las imágenes de certificado (no eliminadas) para el curso.
            'cursos' => $curso, // Los datos del curso encontrado.
        ]);
    }

    /**
     * Actualiza la información de un curso existente.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene los datos a actualizar.
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        // Busca un curso en la base de datos utilizando el UUID proporcionado.
        // - where('uuid_curso', $id): Busca un registro donde la columna 'uuid_curso' coincida con el valor del parámetro '$id'.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ningún curso con ese UUID, lanza una excepción NotFoundHttpException (error 404).
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        // Obtiene solo los datos del request que corresponden a los campos que se van a actualizar.
        // - 'titulocer': El nuevo título del certificado.
        // - 'contenidocer': El nuevo contenido del certificado.
        // - 'estado': El nuevo estado del curso.
        $data = $request->only(['titulocer', 'contenidocer', 'estado']);
        // Actualiza los atributos del modelo del curso con los datos obtenidos del request.
        $cursos->update($data);
        // Redirige al usuario a la página anterior (la página desde donde se envió el formulario).
        // - with('editado', 'Distrito editada correctamente'): Agrega un mensaje a la sesión para mostrar una notificación de éxito.
        // - with('datos_array', [$data]): Agrega un array con los datos actualizados a la sesión (esto puede ser útil para mostrar los cambios al usuario).
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }

    /**
     * Actualiza las imágenes y el estado de un curso, e inactiva una imagen de certificado específica.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene los datos de las imágenes y el estado.
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update2(Request $request, $id)
    {

        // Busca un curso en la base de datos utilizando el UUID proporcionado.
        // - where('uuid_curso', $id): Busca un registro donde la columna 'uuid_curso' coincida con el valor del parámetro '$id'.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ningún curso con ese UUID, lanza una excepción NotFoundHttpException (error 404).
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();

        // Obtiene solo los datos del request correspondientes a los campos de las imágenes y el estado del curso.
        // - 'img_cer': La imagen del certificado.
        // - 'img_cerlogo': El logo del certificado.
        // - 'img_firma1' a 'img_firma4': Las imágenes de las firmas.
        // - 'img_sello': La imagen del sello.
        // - 'estado': El estado del curso.
        $data = $request->only(['img_cer', 'img_cerlogo', 'img_firma1', 'img_firma2', 'img_firma3', 'img_firma4', 'img_sello', 'estado']);
        // Actualiza los atributos del modelo del curso con los datos de las imágenes y el estado obtenidos del request.
        $cursos->update($data);
        // Busca una imagen de certificado específica utilizando el UUID proporcionado en la petición.
        // - where('uuid_imgcer', $request->uuid_imgcer): Busca un registro donde la columna 'uuid_imgcer' coincida con el valor del campo 'uuid_imgcer' en la petición.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ninguna imagen con ese UUID, lanza una excepción NotFoundHttpException (error 404).
        $imagenes = Imagencertificado::where('uuid_imgcer', $request->uuid_imgcer)->firstOrFail();
        // Actualiza el estado de la imagen de certificado encontrada a 'inactivo'.
        $imagenes->update(['estado' => 'inactivo',]);
        // Redirige al usuario a la página anterior (la página desde donde se envió el formulario).
        // - with('editado', 'Distrito editada correctamente'): Agrega un mensaje a la sesión para mostrar una notificación de éxito (nota: el mensaje podría ser más específico, como 'Imágenes del certificado actualizadas correctamente').
        // - with('datos_array', [$data]): Agrega un array con los datos actualizados del curso a la sesión.
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }

    /**
     * Actualiza las imágenes y el estado de un curso, y activa una imagen de certificado específica.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene los datos de las imágenes y el estado.
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update3(Request $request, $id)
    {
        // Busca un curso en la base de datos utilizando el UUID proporcionado.
        // - where('uuid_curso', $id): Busca un registro donde la columna 'uuid_curso' coincida con el valor del parámetro '$id'.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ningún curso con ese UUID, lanza una excepción NotFoundHttpException (error 404).
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        // Obtiene solo los datos del request correspondientes a los campos de las imágenes y el estado del curso.
        // - 'img_cer': La imagen del certificado.
        // - 'img_cerlogo': El logo del certificado.
        // - 'img_firma1' a 'img_firma4': Las imágenes de las firmas.
        // - 'img_sello': La imagen del sello.
        // - 'estado': El estado del curso.
        $data = $request->only(['img_cer', 'img_cerlogo', 'img_firma1', 'img_firma2', 'img_firma3', 'img_firma4', 'img_sello', 'estado']);
        // Actualiza los atributos del modelo del curso con los datos de las imágenes y el estado obtenidos del request.
        $cursos->update($data);
        // Busca una imagen de certificado específica utilizando el UUID proporcionado en la petición.
        // - where('uuid_imgcer', $request->uuid_imgcer): Busca un registro donde la columna 'uuid_imgcer' coincida con el valor del campo 'uuid_imgcer' en la petición.
        // - firstOrFail(): Ejecuta la consulta y, si no se encuentra ninguna imagen con ese UUID, lanza una excepción NotFoundHttpException (error 404).
        $imagenes = Imagencertificado::where('uuid_imgcer', $request->uuid_imgcer)->firstOrFail();
        // Actualiza el estado de la imagen de certificado encontrada a 'activo'.
        $imagenes->update(['estado' => 'activo',]);
        // Redirige al usuario a la página anterior (la página desde donde se envió el formulario).
        // - with('editado', 'Distrito editada correctamente'): Agrega un mensaje a la sesión para mostrar una notificación de éxito (nota: el mensaje podría ser más específico, como 'Imágenes del certificado actualizadas correctamente').
        // - with('datos_array', [$data]): Agrega un array con los datos actualizados del curso a la sesión.
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }
}
