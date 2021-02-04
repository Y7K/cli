# Y7K CLI

## Installation

1. Clone this repository: `git clone <repo-url> <directory>`

2. Change into the  directory and run `composer install`

3. Place the directory path in your PATH, e.g. by adding
    ```
    export PATH=$PATH:$HOME/path/to/cli
    ```
    to your `~/.bash_profile` or `~/.zshrc` file.

    This will allow you to run the `y7k` command globally.


## Update

To update the CLI, pull the latest version and install the composer dependencies.
```
git pull
composer install
```

## Commands

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

## Content Sync

### y7k db:pull
Pulls the database from a remote installation

```
y7k db:pull [ENV_NAME]
y7k db:pull pizza
y7k db:pull pizza -f // Forced (no need to confirm )
y7k db:pull production
y7k d:pul pizza // Short Version
```

This will overwrite your local database with the one from the [ENV_NAME], defined in `.y7k-cli.yml` of the project.

### y7k db:push
Pushes the database to a remote installation

```
y7k db:push [ENV_NAME]
y7k db:push pizza
y7k db:push production
y7k d:pus pizza // Short Version
```

This will overwrite the remote database [ENV_NAME] with your local one. The remote is defined in `.y7k-cli.yml` of the project.

### y7k assets:pull
Pulls storage files from a remote installation

```
y7k assets:pull [ENV_NAME]
y7k assets:pull pizza
y7k assets:pull pizza -f // Forced (no need to confirm )
y7k assets:pull production
y7k a:pul pizza // Short Version
```

This will rsync files from a remote destination [ENV_NAME] to local, defined in `.y7k-cli.yml` of the project.


### y7k assets:push
Pushes the database to a remote installation

```
y7k assets:push [ENV_NAME]
y7k assets:push pizza
y7k assets:push production
y7k a:pus pizza // Short Version
```

This will rsync files from local to a remote destination [ENV_NAME], defined in `.y7k-cli.yml` of the project.


### y7k content:pull
Combination of `db:pull` and `assets:pull`

```
y7k content:pull [ENV_NAME]
y7k content:pull pizza
y7k content:pull pizza -f // Forced (no need to confirm )
y7k content:pull production
y7k c:pul pizza // Short Version
```



### y7k content:push
Combination of `db:push` and `assets:push`

```
y7k content:push [ENV_NAME]
y7k content:push pizza
y7k content:push production
y7k c:pus pizza // Short Version
```

-----

Originally inspired by the [Kirby CLI](https://github.com/getkirby/cli), since v3.0 based on [Laravel Zero](http://laravel-zero.com/).
