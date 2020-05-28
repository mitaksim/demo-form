**28/05/2020**

1. Pour lancer le projet avec Symfony déjà installé :
    ```
    composer install
    ```
2. Il y a eu une mise à jour de Symfony entre temps, alors il faut faire un update de ce qui a été changé :
    ```
    composer update
    ```
3. Dans **TodoController** on commence par définir les méthodes (GET ou POST) qu'on va utiliser pour nos routes, on ajoute alors dans les annotations de chaque méthode du contrôler la méthode souhaitée.

    Doc : https://symfony.com/doc/current/routing.html#matching-http-methods

4. Juste pour info : **bin/console debug:router** analyse toutes les routes disponibles. Si jamais on a une route qui ne marche pas, on pourra ouvrir le debug pour voir si on trouve le problème.  

    Pour vérifier une route précise : **bin/console debug:router "nom_de_la_route"**

5. Pour récupérer les paramètres POST, on nous demande de passer par l'objet**Request** :

    Doc : https://symfony.com/doc/current/controller.html#the-request-and-response-object

    - On commence par faire un **use** de cette classe :
        ```
        use Symfony\Component\HttpFoundation\Request;
        ```
    - On passe l'objet **Request** en paramètre de la méthode **todoAdd** :
        ```
        public function todoAdd(Request $request)
        ```
        La variable $request est un objet de la classe Request.On   peu l'utiliser grâce au *use*.

        Si on veut récupérer des données en GET :
        ```
        $request->query->get('page');
        ```
        Si on veut récupérer des données en POST :
        ```
        $request->request->get('page');
        ```
6. On teste en faisant un **dump($request->request)**. 
    - On tape quelque chose dans l'input pour ajout d'une nouvelle tâche.

    - On voit bien qu'on récupére un *ParameterBag* avec la tâche qu'on vient "d'ajouter". 

    - Dans Symfony quand il y a le nom Bag ça veut dire que la classe a des **getters et setters**. 

        On pourra alors, récupérer la tâche que l'on vient d'ajouter :
        ```
        $request->query->get('task');
        ```

