# generator-bundle

Il permet de générer un bundle à l'aide d'une commande (`./app/console profideo:generate-bundle`) et de les activer
dans le noyau de l'application (`./app/AppKernel.php`).

Le bundle doivent être définis dans la configuration de l'application symfony.

D'autres part, un bundle parent peut être défini pour le bundle ainsi qu'un préfixe qui sera ajouté au nom de
la classe du bundle.

La configuration doit être renseignée dans un des fichiers de configuration de l'application. Comme c'est une commande
destinée aux développeurs, le plus judicieux est de le définir dans le fichier `app/config_dev.yml`.

Un exemple de configuration serait  :

```
profideo_generator:
    name: bar
    base_namespace: AcmeBis\Bundles
    parent: ParentBisBundle          #optionnel
    class_prefix: AcmeBis            #optionnel
```

La configuration précédente génère l'architecture de bundle suivant :

```
src/
    Acme/
        Bundles/
            FooBundle/
                AcmeFooBundle.php
```

avec le contenu suivant dans le fichier `src/FooBundle/AcmeFooBundle.php` :

```
namespace Acme\Bundles\FooBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeFooBundle extends Bundle
{
    public function getParent()
    {
        return 'ParentBundle';
    }
}
```
