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

**Flash messages**

**Doc :** https://symfony.com/doc/current/controller.html#flash-messages

Dès qu'un utilisateur sera sur sa session, un message flash apparaîtrat dès qu'il ajoute/supprime une nouvelle tâche pour le prévenir qui la tâche a bien été ajouté/supprimé.

**Note**: **app** c'est une variable toujours disponible dans Twig, elle nous donnera plusierus informations importantes sur notre application.

1. On reste sur la méthode **todoSetStatus()**, où on va faire appel à la méthode **addFlash()** de Symfony.

    Cette méthode attend deux paramètres, la première c'est le **type** de message que l'on souhaite envoyer : *notice*, *success*, *warning*...
    Le deuxième paramètre : c'est le message que l'on soujaite envoyer.

    ```
    $this->addFlash('success', 'La tâche #' .$id. ' est bien marqué comme ' .$status);
    ```
2. Quand on coche et décoche les tâches il n'y a rien que se passe, on a pas de messages, parce qu'on a pas encore créer la vue pour les afficher.

    Pour l'exemple, on va dans le template *index.html.twig* et on colle le dernier exemple qu'ils ont mis dans la doc pour l'affichage de la vue : 
    ```
    {% for label, messages in app.flashes %}
    {% for message in messages %}
        <div class="flash-{{ label }}">
            {{ message }}
        </div>
	    {% endfor %}
    {% endfor %}
    ```
3. Maintenant, on peut tester en allant à la page d'accueil il y aura bien tous les messages concernant les actions que l'ont a faites.
    ```
    La tâche #1 est bien marqué comme done
    La tâche #2 est bien marqué comme done
    La tâche #2 est bien marqué comme undone
    ```
4. En commentant le code ci-dessus :
    ``` 
    {{ dump(app) }}
    ```

    On accéde à toutes les propriétés de **app**. On va chercher **session**, jusqu'à ce qu'on tombe sur 
    ```
    "_symfony_flashes" => &3 []
    ```
    Là, on voit que notre tableau est vide, alors on fait quelques changements en cochant et en décochant les tâches et on actualise le dump. Là on voit que ça a changé et on retrouve bien dans le tableau toutes les tâches cochés/décochés.

5. Toutes ces manips pour comprendre ce qu'on peut trouver quand on fait une boucle dans **app.flashes** :
    
    - *messages* : ça correspond à l'ensemble des messages que l'on peut trouver, soit: success, warning, etc...

    - dans la deuxième boucle, on boucle la liste des messages par type. Dans notre cas, il n'y a qu'une : *success*.

6. L'exemple c'était juste pour montrer qu'on peut les afficher n'importe où, maintenant on peut les effacer.

    Le plus intéressant c'est d'afficher ces messages quelque part dans la vue **list.html.twig**. Pour savoir l'endroit exacte, on vérifie les maquettes.

7. Le message doit être ajouté juste avant la liste des tâches.
    ```
    {% for messageType in app.flashes %}
    {% for message in messageType %}
        {{ message }}
    {% endfor %}
    ```

    Pour qu'au niveau du style ça ressemble à la maquette on va utiliser **Bootstrap 3.3.7**. Dans **Components** on va utiliser les **alertes**.

    Le code pour les alertes success :
    ```
    <div class="alert alert-success"> role="alert">...</div>
    ```

    C'est à l'endroit où il y a les 3 poitns qu'on va mettre nos messages.

8. On teste, en cochant et en décochant les cases et il y a bien les alertes messages qu'apparaîssent :grinning:

9. On pourrait utiliser les boucles avec la méthode **clé valeur** aussi.

    *cf. commit*

10. Maintenant, on pourra faire la même chose pour les autres méthodes, ajouter le message quand une nouvelle tâche a été ajouté par exemple :

    - On commence par créer une nouvelle variable **$tasks** où on va stocker la réponse de la requête get('task').
    ```
    $tasks = $request->request->get('task');
    ```

    - On change la demande de requête qu'avant était dans l'appel à la méthode **add()** :
    ```
    $task->add($tasks);
    ```

    - Et on ajoute le message avec **addFlash()** :
    ```
    $this->addFlash('success', 'La tâche "' .$tasks. '" a bien été ajoutée.');
    ```

**23/06/2020**

1. On continue à coder la fonction **todoAdd**, en mettant ce qu'il faut faire si le champ de l'input est vide.

    ```
     $tasks = $request->request->get('task');

        if (empty($tasks))  {
            $this->addFlash('danger', 'La tâche ' .$tasks. 'est vide et n\'a pas été ajouté.');
        } else {
            $task = new TodoModel();
            $task->add($tasks);
            
            $this->addFlash('success', 'La tâche "' .$tasks. '" a   bien été ajoutée.');
        }
        
        return $this->redirectToRoute('todo_list');
    }
    ``` 

2. On peut mettre un **trim()** aussi dans la requête qui va récupérer la tâche pour qu'il prenne pas en compte les espaces.

    ``` 
    $tasks = trim($request->request->get('task'));
    ```
3. On teste et ça marche bien! 

4. On passe à la suppression d'une tâche.

    On la fait avec la méthode POST, parce qu'on fait un changement dans la bdd. En méthode GET ça permettrait que quelqun passe d'autres informations dans URL, ce qui pourrait effacer plusieurs données de notre base. C'est une petite protection.

5. On va vérifier notre template **list.html.twig** et on va changer le *action* de **fomr** qui n'a pas été renseigné.

    ```
    <form action="{{ path('todo_delete' }}" method="post">
        <button class="btn btn-xs btn-link" type="submit"name="delete"><span class="glyphiconglyphicon-remove"></span></button>
    </form>
    ```

    La route n'existe pas encore, on va la créer.

6. Dans **TodoController** on vient créer cette route :

    ```
    public function todoDelete() {

    }
    ```

7. On fait un C/C du **doc BLock** de l'ajout d'une tâche.

8. Comme on veut récupérer les informations de notre requête, il nous faut un objet *Request*. Avec l'objet Request on va pouvoir trouver l'objet en question.

    ```
    public function todoDelete(Request $request) {
        $id = $request->request->get('task_id);
    }
    ```

    Il faut qu'on vérifie si **l'id** est bien fourni, si la tâche existe et si c'est bien un entier.

9. Maintenant, dans le **form** de **list.html.twig** on va ajouter un **input** :

    ```
    <input type="hidden" name="task_id" value="{{ key }}">
    ```

10. On teste en inspectant les tâches et il y a bioen que la value qui change avec l'id de chaque tâche.

11. On doit vérifier qu'on a bien reçu l'id, ça veut dire le GET ne renvoi pas **false**, si c'est bien un **entier** :

    ```
    if ($id != false && is_numeric($id)) 
    ```

    Et que la tâche existe :

    ```
    $task = TodoModel::find($id);

    if ($id != false && is_numeric($id) && $task != false)
    ```

12. Si les conditions son bien respectées, on appelle la méthode **delete()** de **TodoModel** :

    ``` 
    TodoModel::delete($id);

    $this->addFlash('success', 'La tâche a bien été supprimée);
    ```

13. Si les consitions sont pas respectées :

    ```
    } else {
        $ths->addFlash('danger, 'Une erreur est survenu, la tâche n\'a pas été supprimée);
    }
    ```

14. Après la suppression, on redirigera l'utilisateur vers la page des listes.

    ```
    return $this->redirectToRoute('todo_list);
    ```

15. On teste et on arrive bien à supprimer les tâches. La seule tâche qu'on arrive pas à supprimer c'est la première avec l'indice 0.

    **Rappel** : 
    
    Equivalents de false : 

        - 0
        - Chaîne vide
        - Tableau vide
        - Objet vide

    Si, dans notre condition on met **$id !== false** ça va marcher, parce que là on va vérifier si le type et la valeur sont différents.

16. Maintenant ça marche. Mais, dès que tout est supprimée, les tâches de bases reviennent toutes seules. On verra ça après.

## Templates

1. Dans **list.html.twig** on va améliorer le bout de code qui concerne les tâches effectuées ou pas. Juste après le *form*.

    On commence par copier la première partie : 

    ``` 
    <a href="{{ path('todo_set_status', { 'id' : key, 'status' : 'undone' } ) }}"><span class="glyphicon glyphicon-check"></span></a>
    ```

    On doit trouver une solution pour savoir si la tâche est **done** ou **undone** et l'écrire sur une seule ligne.

    En regardant la *doc* de **Twig**, on voit qu'on a la possibilité d'écrire un ternaire directement dans le **html** comme on fait en PHP.

    ```
    <a href="{{ path('todo_set_status', { 'id' : key, 'status' : (todo.status == 'done' ? 'undone' : 'done') } ) }}"><spanclass="glyphicon glyphicon-check"></span></a>
    ```

2. On teste et ça marche. Il nous met bien deux checkboxs (comme on l'a recopié) et on reçoit le bon message si on coche ou pas la case.

3. On pourrait faire la même chose pour la *classe* si **check** ou **unchecked** :

    ```
    <spanclass="glyphicon glyphicon-{{ (todo.status == 'done' ? 'check' : 'unchecked'}}"></span>
    ```
4. On teste et ça marche aussi!

    **Rappel** : On peut appeller les *includes* de Twig **partials**.

### Form

1. Dans **todo**, on va créer le dossier **partials** avec un fichier **form_add** où on va mettre le formulaire qui contient toutes les informations pour l'ajout d'une tâche. Il se trouve dans le dossier **list.html.twig**.

    **Rappel** : Dans Twig quand on utilise :
        
        - {% %} : utilisé plutôt pour la structure, pour faire des boucles, des **ifs*

        - {{ }} : utilisé pour afficher une information 

    Dans notre cas, on aura besoin d'aller chercher un autre bout de template, on va "informer" où il se trouve dans le code.

    ``` 
    {{ include('todo/partials/form_add.html.twig') }}
    ```

2. On teste, et on retrouve bien notre formulaire. 

    Pour être sûre que le code a marché, on peut taper quelque chose dans le code du formluaire pour voir si ça s'affiche sur l'écran.

3. Maintenant, on fait la même chose avec le *form* qui est dans le fichier **index.html.twig**.

    L'avantage de factoriser ce formulaire comme d'habitude, c'est que si un jour on a besoin de changer quelque chose il suffira de changer dans un seul fuchier qui ça prendra en compte tous les fichiers où il y a un **include**.

4. On pourra passer aussi, le nom du titre de façon dynamique.

    ```
    <h3>{{ form_add_title }}</h3>
    ```

    Cette variable n'existe pas encore, on va la créer.

5. On va mettre ce code non pas directement dans notre fichier **form_add**, mais dans les fichiers **index.html.twig** et **list.html.twig**. Comme ça chacun pourra avoir un titre différent.

     ```
    {% set form_add_title = 'Vous pouvez ajouter une tâche ici' %}
    ```

6. Ca marche comme ça, mais c'est pas la bonne façon de faire, le mieux serait de faire comme ceci :

    ```
    {{ include'todo/partials/form_add.html.twig', {form_add_title: 'Ajout d\'une tâche' }) }}
    ```

    On passe la variable que l'on veut utiliser dans le template comme deuxième paramètre de la fonction.

### Titre de page différent

1. Dans notre **base.html.twig**, on a déjà le bloc :

    ```
    <title>{% block title %}{% endblock %}</title>
    ```

    Alors, il suffit de reprendre ce bloc dans chaque template où on veut changer de nom.

2. On commence par **index.html.twig**, où on veut mettrre le nom de l'application:

    ```
    {% block title %}TodoApp{% endblock %}
    ```

3. Maintenant, on veut que dans la page *Détail de la tâche* **single.html.twig** l'intitulé de la tâche soit le titre de la page.

    - On fait un C/C du bloc :

    ```
    {% block title %}TodoApp{% endblock %}
    ```

    - et on récupère l'intitulé de la tâche directement dans le code :

    ```
    {% block title %} {{todo.task }} {% endblock %}
    ```









