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
    - On reteste, on récupéres bien le nom qu'on avait tapé dans l'input.

7. Pour pouvoir ajouter vraiment une nouvelle tâche, on doit instancier la classe **TodoModel** qui contient la méthode **add()**.
    ```
    $task = new TodoModel();
    $task->add($request->request->get('task'));
    ```

    On teste en tapant à nouveau une tâche, il y a une erreur, parce que le code est pas encore optimal. Mais quand on revient sur la page d'ajout et on actualise la page il y a bien la tâche qui s'est rajouté.

8. On souhaite maintenant rediriger l'utilisateur vers la page où se trouvent toutes les tâches, pour ça on va utiliser la fonction **redirect()** de Symfony :
    ```
    return $this->redirectToRoute('todo_list');
    ```

    On teste et ça marche! 

**31/05/2020**

1. On nous demande de définir la route en *annotation* pour la méthode **todoSetStatus**. 

    **Commentaire personnel**: En suivant tous les pas du prof, j'arrive pas à trouver la même chose. D'abord j'avais une erreur concernant l'existance ou pas du fichier **annotations.yaml**, c'était pas les cas, alors je fais un : 
    ```
    composer require annotations
    ```

    Je sais pas si c'était vraiment utile, vu qu'avant les routes en annotation marchait bien. Mais j'ai plus le message d'erreur qu'apparaît.

    **Erreur debug Symfony**

    Je continue à avoir des erreurs, cette fois concernant le debug de Symfony qui a été déprecié entre temps, je fais une recherche Google et on nous dit de faire juste un composer de debug :
    ```
    composer require Symfony/debug
    ```

    **Erreur Doctrine**

    *User Deprecated: Creating Doctrine\ORM\Mapping\UnderscoreNamingStrategy without making it number aware is deprecated and will be removed in Doctrine ORM 3.0.*

    En faisant des recherches, je suis tombé sur cette solution : 

    1. Dans le dossier **packages/doctrine.yaml** changer la première ligne par la deuxième en ajoutant *_number_aware*:
        ```
        naming_strategy: doctrine.orm.naming_strategy.underscore
  
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        ```

**02/06/2020**

Commentaire personnel : Finalement, la dernière fois j'ai pas eu le temps de faire grand chose à part corriger les erreurs. 

1. Pour pouvoir définir le statut d'une tâche en *done* ou *undone* comme demandé dans l'énoncé en utilisant les **requirements** :

    cf. **doc** : https://symfony.com/doc/current/routing.html#special-parameters

    ```
    /**
     * Changement de statut
     *
     * @Route(
     *  "/todo/{id}/{status}", name="todo_set_status", requirements={"id": "\d+", "status": "done|undone"}, methods={"GET"})
     */
     ```
2. On teste en tapant la **route/todo/2/done**, on a une erreur mais il a trouvé quand même la route, le message suivant apparaît :

    ```
    No route found "GET/todo/2/done":Method Not Allowed (Allow: POST)
    ```

3. Comme on a mis une contrainte, la route accepte juste *done* ou *undone*, si on met une autre chose, Symfony va pas trouver la route et le message suivant apparaît:
    ```
    No route found for "GET /todo/2/nimportequoi
    ```

4. Comme vu dans le message d'erreur, la méthode HTTP à utiliser ce sera plutôt du **GET**, parce qu'il va nous falloir cocher la case quand la tâche serait *done*, alors on la change dans notre **annotation**

5. Pour que ça marche vraiment, on doit instancier la classe **TodoModel** et appeler la méthode **setSatus** en passant en paramètre l'id de la tâche à changer le status et le status lui même.
    ```
    TodoModel::setStatus($id, $status);
    ```
    Maintenant on aura plus à vérifier si la tâche est faite ou pas, avec l'annotation c'est Symfony que le fera pour nous.


