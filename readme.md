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

## Update

To update the CLI, pull the latest version:
```
git pull
```

## Commands

### y7k new

Install a new Project from the [Y7K Boilerplate](https://github.com/Y7K/plate).

```
y7k new
```

Launches the installation process. You will be prompted for a name and to configure the project stack.
You can specify the directory with the second argument and a platform with the `--platform` option:

```
y7k new pr01-project
y7k new pr01-project --platform craft
y7k new pr01-project --platform laravel
y7k new pr01-project --platform plain
```


### y7k version 

Display the current Version of the Project.

```
y7k version
y7k v 
```

### y7k bump 

Bump the current Version of the Project.

```
y7k bump [major|minor|patch]
y7k b [major|minor|patch]
```

### y7k storage:link 

Create a symbolic link from "public/storage" to "storage/app/public".

```
y7k storage:link 
```

### y7k craft:update 

Update to the latest version of Craft.
This command deletes the `craft/app` directory and replaces it with the most recent version. Make sure to log into the control panel after performing the update to ensure the database is updated, too.

```
y7k craft:update
```

## Roadmap

* Add Commands to update Craft & Plugins

-----

Inspired by the [Kirby CLI](https://github.com/getkirby/cli)

