<?php
require_once "modelos/cursos.modelo.php";

class ControladorCursos
{



    public function index($pagina)
    {

        //validar credenciales del cliente

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $value) {
                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"])) {

                    if($pagina !=null){

                        $cantidad = 10;
                        $desde = ($pagina-1)*$cantidad;

                        $cursos = ModeloCursos::index("cursos","clientes",$cantidad,$desde);
                    }else{
                        $cursos = ModeloCursos::index("cursos","clientes",null,null);
                    }
                    $json = array(
                        "status" => 200,
                        "total_registros" => count($cursos),
                        "detalle" => $cursos
                    );
                    echo json_encode($json, true);
                    return;
                }
            }
        }
    }

    public function create($datos)
    {

        //validar credenciales del cliente
        $clientes = ModeloClientes::index("clientes");
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {
                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == base64_encode($valueCliente["id_cliente"] . ":" . $valueCliente["llave_secreta"])) {
                    foreach ($datos as $key => $valueDatos) {
                        if (isset($valueDatos) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $valueDatos)) {
                            $json = array(
                                "status" => 404,
                                "detalle" => "Error en el campo " . $key
                            );
                            echo json_encode($json, true);
                            return;
                        }
                    }
                    //Validar que el titulo o la descripcion no esten repetidos

                    $cursos = ModeloCursos::index("cursos", "clientes",null,null);
                    foreach ($cursos as $key => $value) {
                        if ($value->titulo == $datos["titulo"]) {
                            $json = array(
                                "status" => 404,
                                "detalle" => "El titulo ya existe en la base de datos"
                            );
                            echo json_encode($json, true);
                            return;
                        }
                        if ($value->descripcion == $datos["descripcion"]) {
                            $json = array(
                                "status" => 404,
                                "detalle" => "La descripcion ya existe en la base de datos"
                            );
                            echo json_encode($json, true);
                            return;
                        }
                    }
                    //llevar datos al modelo

                    $datos = array(
                        "titulo" => $datos["titulo"],
                        "descripcion" => $datos["descripcion"],
                        "instructor" => $datos["instructor"],
                        "imagen" => $datos["imagen"],
                        "precio" => $datos["precio"],
                        "id_creador" => $valueCliente["id"],
                        "created_at" => date('Y-m-d h:i:s'),
                        "updated_at" => date('Y-m-d h:i:s')
                    );

                    $create = ModeloCursos::create("cursos", $datos);
                    //respuesta del modelo

                    if ($create == "OK") {
                        $json = array(
                            "status" => 200,
                            "detalle" => "Registro exitoso, su curso ha sido guardado"
                        );
                        echo json_encode($json, true);
                        return;
                    }
                }
            }
        }

        $json = array(
            "detalle" => "estas en la vista cursos create"
        );

        echo json_encode($json, true);
        return;
    }

    public function show($id)
    {


        //validar credenciales del cliente
        $clientes = ModeloClientes::index("clientes");
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {
                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == base64_encode($valueCliente["id_cliente"] . ":" . $valueCliente["llave_secreta"])) {

                    //Mostrar todos los cursos
                    $curso = ModeloCursos::show("cursos", "clientes",$id);

                    if (!empty($curso)) {
                        $json = array(
                            "status" => 200,
                            "detalle" => $curso
                        );

                        echo json_encode($json, true);
                        return;
                    } else {
                        $json = array(
                            "status" => 404,
                            "detalle" => "No hay informacion"
                        );

                        echo json_encode($json, true);
                        return;
                    }
                }
            }
        }
    }

    public function update($id, $datos)
    {
        $clientes = ModeloClientes::index("clientes");
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {
                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == base64_encode($valueCliente["id_cliente"] . ":" . $valueCliente["llave_secreta"])) {

                    //validar datos
                    foreach ($datos as $key => $valueDatos) {
                        if (isset($valueDatos) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $valueDatos)) {
                            $json = array(
                                "status" => 404,
                                "detalle" => "Error en el campo " . $key
                            );
                            echo json_encode($json, true);
                            return;
                        }
                    }
                    //validar id creador
                    $curso = ModeloCursos::show("cursos", "clientes", $id);

                    foreach ($curso as $key => $valueCurso) {

                        if ($valueCurso->id_creador == $valueCliente["id"]) {

                            /*=============================================
							Llevar datos al modelo
							=============================================*/

                            $datos = array(
                                "id" => $id,
                                "titulo" => $datos["titulo"],
                                "descripcion" => $datos["descripcion"],
                                "instructor" => $datos["instructor"],
                                "imagen" => $datos["imagen"],
                                "precio" => $datos["precio"],
                                "updated_at" => date('Y-m-d h:i:s')
                            );

                            $update = ModeloCursos::update("cursos", $datos);

                            if ($update == "OK") {

                                $json = array(
                                    "status" => 200,
                                    "detalle" => "Registro exitoso, su curso ha sido actualizado"

                                );

                                echo json_encode($json, true);

                                return;
                            } else {

                                $json = array(

                                    "status" => 404,
                                    "detalle" => "No está autorizado para modificar este curso"

                                );

                                echo json_encode($json, true);

                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    public function delete($id){
        $clientes = ModeloClientes::index("clientes");
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {
                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == base64_encode($valueCliente["id_cliente"] . ":" . $valueCliente["llave_secreta"])) {

                    //validar id creador

                    $curso = ModeloCursos::show("cursos","clientes",$id);
                    foreach($curso as $key => $valueCurso){
                        if($valueCurso->id_creador == $valueCliente["id"]){

                            //llevar los datos

                            $delete = ModeloCursos::delete("cursos",$id);
                            if ($delete == "OK") {
                                $json = array(
                                    "status" => 200,
                                    "detalle" => "Se ha borrado el curso"
                                );
                                echo json_encode($json, true);
                                return;
                            } else {
                                $json = array(
                                    "status" => 404,
                                    "detalle" => "No está autorizado para modificar este curso"
                                );
                                echo json_encode($json, true);
                                return;
                        }
                    }
                }
            }
        } 
    }
}
}