<?php

$arrayRutas = explode("/", $_SERVER['REQUEST_URI']);

if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {

    $cursos = new ControladorCursos();
    $cursos->index($_GET["pagina"]);
} else {

    

    if (count(array_filter($arrayRutas)) == 2) {

        //Cuando no se hace ninguna peticion a la api

        $json = array(
            "detalle" => "no encontrado"
        );

        echo json_encode($json, true);
        return;
    } else {

        //Cuando pasamos solo un indice en el array $arrayRutas

        if (count(array_filter($arrayRutas)) == 3) {

            //Cuando se hace peticiones desde cursos

            if (array_filter($arrayRutas)[3] == "cursos") {

                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {

                    //Capturar datos
                    $datos = array(
                        "titulo" => $_POST["titulo"],
                        "descripcion" => $_POST["descripcion"],
                        "instructor" => $_POST["instructor"],
                        "imagen" => $_POST["imagen"],
                        "precio" => $_POST["precio"]
                    );

                    //  echo "<pre>"; print_r($datos); echo "<pre>";
                    $cursos = new ControladorCursos();
                    $cursos->create($datos);
                } else if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "GET") {
                    $cursos = new ControladorCursos();
                    $cursos->index(null);
                }
            }

            //Cuando se hace peticiones desde registro

            if (array_filter($arrayRutas)[3] == "registro") {
                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {

                    $datos = array(
                        "nombre" => $_POST["nombre"],
                        "apellido" => $_POST["apellido"],
                        "email" => $_POST["email"]
                    );


                    $cliente = new ControladorClientes();
                    $cliente->create($datos);
                }
            }
        } else {
            if (array_filter($arrayRutas)[3] == "cursos" && is_numeric(array_filter($arrayRutas)[4])) {
                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "GET") {

                    $cursos = new ControladorCursos();
                    $cursos->show(array_filter($arrayRutas)[4]);
                }
                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "PUT") {

                    $datos = array();
                    parse_str(file_get_contents('php://input'), $datos);

                    $cursos = new ControladorCursos();
                    $cursos->update(array_filter($arrayRutas)[4], $datos);
                }
                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "DELETE") {
                    $cursos = new ControladorCursos();
                    $cursos->delete(array_filter($arrayRutas)[4]);
                }
            }
        }
    }
}
