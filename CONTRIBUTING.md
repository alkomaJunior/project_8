#Contribution

Please take a moment to review this document, so that you can easily follow the contribution process.

##How to contribute?
You saw a **bug**? You thought of an awesome **feature**, that we can add to the application? Then you can contribute by sending ``issues`` or submitting ``pull-requests``.

## Bug report
A bug is a concrete error, caused by the present code in this repository.

Guide:

1. Do not create an existing report, think to use the [search system](https://github.com/bigboss-oualid/project_8/issues).
2. Use the ``prod`` or ``dev`` branch to try the existence of the detected bug.
3. Create a new scénario test with **Behat** or functional test with **PHPUnit**, to identify the bug.
4. Propose new **[issue](https://docs.github.com/en/free-pro-team@latest/github/managing-your-work-on-github/creating-an-issue "Creating an issue")** 
or 
**[pull-request](https://docs.github.com/en/free-pro-team@latest/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request "Creating a pull request")**.

##New feature
It is always appreciated to offering new features. However, take some time to think it over, make sure this functionality matches the objectives of the project.

It is up to you to present a solid argument to convince the project developers of the benefits of this feature.

##Issues
**[Issues](https://docs.github.com/en/free-pro-team@latest/github/managing-your-work-on-github/about-issues "About issues")** is the ideal channel for bug reports, new features or for submitting a ``pull-requests``, however please respect the following restrictions:

* Do not use this channel for your personal help requests (use [Stack Overflow](http://stackoverflow.com/)).
* It is forbidden to insult or offend in any way when commenting on an issue. Respect the opinions of others, and stay focused on the main discussion.

##Pull request
Good pull requests are a precious help to us. They should be within the range of the project and should not contain any ``commits`` unrelated to the project.

Please ask us before posting your pull request, otherwise you may waste your working time, if the project team does not want to integrate your work.

Follow this process to propose a pull request that respects best practices:
1. [Fork](http://help.github.com/fork-a-repo/) the project, clone your fork and configure the ``remotes``:
```bash
git clone https://github.com/<your-username>/<repo-name>
cd project_8
git remote add upstream https://github.com/bigboss-oualid/project_8
```

2. If you cloned the project a while ago, remember to update the latest changes from `upstream`:
```bash
git checkout prod
git pull upstream prod

git checkout dev
git pull upstream dev
```

3. Follow the [instructions](docs/github_pages/instal.md) to install the project.

4. Create a new branch that will contain your feature, modification or bug fix :
* For a **new feature** or **modification** :
```bash
git checkout dev
git checkout -b feature/<feature-name>
```
* For a fix :
```bash
git checkout prod
git checkout -b hotfix/<feature-name>
```
>Optional : use [git-flow](https://danielkummer.github.io/git-flow-cheatsheet/index.html), to simplify the management of your branches.

5. Commit your changes. The naming convention for your ``commits`` should comply with:

```
<type>(<scope>): <subject>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```
>The first line (``Header``) is mandatory.

**Types**: write what your commit does.
* chore: Changes that have an effect on the system (installation of new dependencies, composer, npm, environments, ...).
* ci: Configure Continuous Integration.
* docs: changes only in the documentation.
* feat: a new feature.
* fix: a bug fix.
* perf: changes that improves performance.
* refactor: changes that is neither a bug fix nor development but only a refactoring of the code.
* style: changes that does not affect the meaning of the code (space, reformatting, alignments in the code…).
* test: add missing tests.

**Scope**: (_optional_) describes the scope of your change. Possible values are the names of your components for example: Auth, tutorial, forum, contact. Can be anything specifying the location of the validation change. For example: $ location, $ browser, $ compile, $ rootScope, etc…

**Subject**: contains a brief description of your changes. Verbs must be imperative. No capital letter at the beginning and no point at the end.

**Body**: should describe the motivation for the changes from the previous commit.
>Use body only if you can't describe your changes in **one line** in ``subject``.

>Use imperative verbs & describe what the changes does and why? (without commenting on the content = read the code?).

**Footer**:(_optional_) describe the fixed bugs and therefore references the bug number. It can also be used to describe changes that break things (backwards compatibility for example).

6. Push your branch to your repository:
```bash
git push origin <branch-name> 
```

7. Open a new ``pull request`` with a specific **title** & **description**.
##Don't forget
* use [SOLID](https://openclassrooms.com/fr/courses/6900866-write-maintainable-python-code/7009965-discover-good-programming-practices-with-the-solid-principles)  Principles for a good Programming Practices.
* avoid [STUPID](https://openclassrooms.com/fr/courses/6900866-write-maintainable-python-code/7010365-avoid-stupid-practices-in-programming) Practices in Programming.
* use [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html).
* use [Symfony Coding Conventions](https://symfony.com/doc/current/contributing/code/conventions.html).
* create unit & functional tests.
    * [Behat](https://docs.behat.org/en/latest/ "Visit Documentation")
    * [PHPUnit](https://phpunit.de/ "Visit Documentation")
    * [PHPUnit-bridge](https://symfony.com/doc/current/testing.html "How to create test in Symfony?")
* in the ideal case, you create the UML schemas(UseCase, Class, Sequence, physical data model).
* follow Symfony Directory Structure
    * write your tests in the Top-level ``tests/`` directory.
    * use [Webpack Encore](https://symfony.com/doc/current/frontend.html) to regroup ``JavaScript modules``, pre-processing ``CSS`` & ``JS``, compiling and ``minifying assets``.
    * 

######Thanks for contributing :wave: