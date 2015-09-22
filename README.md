# generator-bundle

Il permet de générer des bundles à l'aide d'une commande (`./app/console profideo:generate-bundle`) et de les activer
dans le noyau de l'application (`./app/AppKernel.php`).

Les bundles doivent être définis dans la configuration de l'application symfony.

D'autres part, un bundle parent peut être défini pour chaque bundle ainsi qu'un préfixe qui sera ajouté au nom de
la classe du bundle.

La configuration doit être renseignée dans un des fichiers de configuration de l'application. Comme c'est une commande
destinée aux développeurs, le plus judicieux est de le définir dans le fichier `app/config_dev.yml`.

Un exemple de configuration serait  :

```
profideo_generator:
    bundles:
        -
            name: foo
            base_namespace: Acme\Bundles
            parent: ParentBundle         #optionnel
            class_prefix: Acme           #optionnel
        -
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
    AcmeBis/
        Bundles/
            BarBundle/
                AcmeBisBarBundle.php
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

et le contenu suivant dans le fichier `src/BarBundle/AcmeBisBarBundle.php` :

```
namespace AcmeBis\Bundles\BarBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeBisBarBundle extends Bundle
{
    public function getParent()
    {
        return 'ParentBisBundle';
    }
}
```

Attention : Si vous souhaitez définir plusieurs bundles ayant un même bundle parent, il faut qu'ils aient le
même "base_namespace". Comme plusieurs bundles ayant un même parent ne peuvent pas être activés en même temps dans le
noyau de l'application, seul le dernier généré le sera.
