.. _install_from_packages:

====================
A partir des paquets
====================

Centreon fournit des RPM pour ses produits au travers de la solution Centreon Open Sources
(ex CES) disponible gratuitement sur notre dépôt.

Ces paquets ont été testés avec succès sur les environnements CentOS et
Red Hat en version 7.x.

***********************
Étapes pré-installation
***********************

SELinux doit être désactivé. Pour cela vous devez modifier le fichier */etc/selinux/config* et remplacer "enforcing" par "disabled" comme dans l'exemple suivant :

::

    SELINUX=disabled

.. note::
    Après avoir sauvegardé le fichier, veuillez redémarrer votre système d'exploitation pour prendre en compte les changements.

Une vérification rapide permet de confirmer le statut de SELinux ::

    $ getenforce
    Disabled

***********************
Installation des dépôts
***********************

Dépôt *Software collections* de Red Hat
-------------------------------------

Afin d'installer les logiciels Centreon, le dépôt *Software collections* de Red Hat doit être activé.

.. note::
    Le dépôt *Software collections* est nécessaire pour l'installation de php 7 et les librairies associées.

Exécutez la commande suivante à partir d'un utilisateur disposant les
droits suffisants.

::

   $ yum install centos-release-scl

Le dépôt est maintenant installé.

Dépôt Centreon
--------------

Afin d'installer les logiciels Centreon à partir des dépôts, vous devez au préalable installer
le fichier lié au dépôt. Exécutez la commande suivante à partir d'un utilisateur disposant de
droits suffisants.

Installation ::

   $ wget http://yum.centreon.com/standard/18.10/el7/stable/noarch/RPMS/centreon-release-18.10-1.el7.centos.noarch.rpm
   $ yum install --nogpgcheck centreon-release-18.10-1.el7.centos.noarch.rpm

Le dépôt est maintenant installé.

*******************************
Installation du serveur central
*******************************

Ce chapitre décrit l'installation d'un serveur central Centreon.

Exécutez la commande :

::

  $ yum install centreon-base-config-centreon-engine centreon

Installer MySQL sur le même serveur
-----------------------------------

Ce chapitre décrit l'installation de MySQL sur un serveur comprenant Centreon.

Exécutez la commande :

::

   $ yum install MariaDB-server
   $ systemctl restart mysql

Système de gestion de base de données
-------------------------------------

La base de données MySQL doit être disponible pour pouvoir continuer l'installation (localement ou non). Pour information nous recommandons MariaDB.

Pour les systèmes CentOS / RHEL en version 7, il est nécessaire de modifier la limitation **LimitNOFILE**.
Changer cette option dans /etc/my.cnf NE fonctionnera PAS.

::

   # mkdir -p  /etc/systemd/system/mariadb.service.d/
   # echo -ne "[Service]\nLimitNOFILE=32000\n" | tee /etc/systemd/system/mariadb.service.d/limits.conf
   # systemctl daemon-reload
   # systemctl restart mysql

Fuseau horaire PHP
------------------

La timezone par défaut de PHP doit être configurée. Pour cela, aller dans le répertoire /etc/opt/rh/rh-php71/php.d et créer un fichier nommé php-timezone.ini contenant par exemple la ligne suivante :

::

    date.timezone = Europe/Paris

Après avoir sauvegardé le fichier, n'oubliez pas de redémarrer le service apache de votre serveur.

::

    systemctl restart httpd

Pare-feu
--------

Paramétrer le pare-feu système ou désactiver ce dernier. Pour désactiver ce dernier exécuter les commandes suivantes :

* **firewalld** ::

    # systemctl stop firewalld
    # systemctl disable firewalld
    # systemctl status firewalld

Lancer les services au démarrage
--------------------------------

Activer le lancement automatique de services au démarrage.

Lancer les commandes suivantes sur le serveur Central ::

    # systemctl enable httpd.service
    # systemctl enable snmpd.service
    # systemctl enable mysql.service
    # systemctl enable rh-php71-php-fpm

.. note::
    Si la base de données MySQL est sur un serveur dédié, lancer la commande d'activation mysql sur ce dernier.

Terminer l'installation
-----------------------

Avant de démarrer la configuration via l'interface web la commande suivante doit être exécutée ::

    # systemctl start rh-php71-php-fpm

Cliquer :ref:`ici <installation_web_ces>` pour finaliser le processus d'installation.

Installer un collecteur
-----------------------

Ce chapitre décrit l'installation d'un collecteur.

Exécutez la commande :

::

    $ yum install centreon-poller-centreon-engine

La communication entre le serveur central et un collecteur se fait via SSH.

Vous devez échanger les clés SSH entre les serveurs.

Si vous n'avez pas de clé SSH privées sur le serveur central pour l'utilisateur 'centreon' :

::

   $ su - centreon
   $ ssh-keygen -t rsa

Vous devez copier cette clé sur le collecteur :

::

    $ ssh-copy-id centreon@your_poller_ip
