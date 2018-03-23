# Y7K CLI

## Installation

1. Clone this repository: `git clone <repo-url> <directory>`

2. Change into the  directory and run `composer install && composer run-script project-created-cmd`

3. Place the directory path in your PATH, e.g. by adding
    ```
    export PATH=$PATH:$HOME/path/to/cli
    ```
    to your `~/.bash_profile` or `~/.zshrc` file.
    
    This will allow you to run the `y7k` command globally.

4. Create a [Personal GitHub Access Token](https://github.com/settings/tokens) with `repo` privileges and add it along with your GitHub Username to the `.env` file.

5. Update the paths of the boilerplate repositories in your `.env` file. To install from local sources, you need to specify the paths (absolute), e.g.

`PATH_SCRIPTS=/Users/yourname/code/plates/scripts`


## Update

To update the CLI, pull the latest version and install the composer dependencies.
```
git pull
composer install
```

## Commands

### y7k new

Install a new Project from the [Y7K Boilerplate](https://github.com/Y7K/plate).

```
y7k new pro01-project
```

Launches the installation process. You will be prompted for a name and to configure the project stack.

Per default, everything is installed from local sources (except vendor repositories like laravel, craft cms etc.). If you want to install from the remote github versions of the plate, add `-r` or `--remote` to the command.

### y7k version 

Display the current Version of the Project.

```
y7k version
```

### y7k bump 

Bump the current Version of the Project. The version will be bumped (project.json) and the change will be commited to git.

Git-Flow: New release created and merged into dev & master
No Git-Flow: Change is commited to current branch
`-g` flag means: Skip all git stuff

```
y7k bump [major|minor|patch] [-g]
y7k b [major|minor|patch] [-g]
```


### y7k storage:link 

Create a symbolic link from "public/storage" to "storage/app/public".

```
y7k storage:link 
```

### y7k craft2:update 

Update to the latest version of Craft 2.*.
This command deletes the `craft/app` directory and replaces it with the most recent version. Make sure to log into the control panel after performing the update to ensure the database is updated, too.

```
y7k craft2:update
```


## Content Sync

### y7k db:pull
Pulls the database from a remote installation

```
y7k db:pull [ENV_NAME]
y7k db:pull pizza
y7k db:pull production
y7k d:pull pizza
```

This will overwrite your local database with the one from the [ENV_NAME], defined in `.y7k-cli.yml` of the project.

### y7k db:push
Pushes the database to a remote installation

```
y7k db:push [ENV_NAME]
y7k db:push pizza
y7k db:push production
y7k d:push pizza
```

This will overwrite the remote database [ENV_NAME] with your local one. The remote is defined in `.y7k-cli.yml` of the project.

### y7k assets:pull
Pulls storage files from a remote installation

```
y7k assets:pull [ENV_NAME]
y7k assets:pull pizza
y7k assets:pull production
y7k a:pull pizza
```

This will rsync files from a remote destination [ENV_NAME] to local, defined in `.y7k-cli.yml` of the project.


### y7k assets:push
Pushes the database to a remote installation

```
y7k assets:push [ENV_NAME]
y7k assets:push pizza
y7k assets:push production
y7k a:push pizza
```

This will rsync files from local to a remote destination [ENV_NAME], defined in `.y7k-cli.yml` of the project.


### y7k content:pull
Combination of `db:pull` and `assets:pull`

```
y7k content:pull [ENV_NAME]
y7k content:pull pizza
y7k content:pull production
y7k c:pull pizza
```



### y7k content:push
Combination of `db:push` and `assets:push`

```
y7k content:push [ENV_NAME]
y7k content:push pizza
y7k content:push production
y7k c:push pizza
```


## y7k components

### y7k components:list

Find existing components. You can also search with a search query. It will return a list of found components


```
y7k components:list
y7k components:list [SEARCH_KEY]
y7k components:list image
```

Per default, everything is searched in local sources. If you want to search remote github repository of components, add `-r` or `--remote` to the command.

```
y7k components:list -r
```


### y7k components:info \[WIP\]
Displays information about a specific component


```
y7k components:info [COMPONENT]
y7k components:info photoswipe
```

Per default, info comes from local sources. If you want to search remote github repository of components, add `-r` or `--remote` to the command.

```
y7k components:info [COMPONENT] -r
```


### y7k components:install
Installs a component. It will copy files into your project. It also tries to apply file merges (update existing files) according to merging rules. Checkout the compnent with `y7k components:info [COMPONENT]` first.


```
y7k components:install [COMPONENT]
y7k components:install photoswipe
```

Per default, the component is installed from local sources. If you want to install from the remote github repository of components, add `-r` or `--remote` to the command.

```
y7k components:install [COMPONENT] -r
```


### y7k components:uninstall \[WIP\]
Uninstalls a component from a project. This means, it removes all component files from your project.

**Warning!**:

- It does not revert file merges. Checkout this files manually to remove traces.
- It does not uninstall NPM or Component packages, nor removes them from package.json or composer.json. You have to do this yourself.


```
y7k components:uninstall [COMPONENT]
y7k components:uninstall photoswipe
```

Per default, the component is uninstalled based on the component info file from local sources. If you want to uninstall with infos from the remote github repository of components, add `-r` or `--remote` to the command.

```
y7k components:uninstall [COMPONENT] -r
```


## Roadmap

* Bring back `composer:info` and `composer:ninstall` commands
* Make `composer:list` able to filter the list again
* Make it optional to install dependencies on component install
* Add more environment commands (like open forge, open deploybot etc.)

-----

Originally inspired by the [Kirby CLI](https://github.com/getkirby/cli), since v3.0 based on [Laravel Zero](http://laravel-zero.com/). 
