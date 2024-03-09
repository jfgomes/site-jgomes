## Adilia project!

![Prod logo](https://cdn-icons-png.flaticon.com/512/2519/2519375.png)

## Introduction

- This is a ready functional prototype to be used in new projects.

- It is in use in my personal website, so all case studies here are real and already implemented.

- This project is for web, is based in PHP ( Laravel ) and all associated services are 100% open source.

- It shows and defines a simple way to create, to develop, to iterate and what we'll need to be well succeed during the development process.

- The local environment needs to work exactly as the production does.

## Diagram overview

![git-branch-protection.png](https://jgomes.site/images/diagrams/schema.drawio.png)

## Details

- This project has 2 environments: local and prod.

- The environment local is only for development.

- The environment prod is only for production.

- In the meddle there's a CI / CD process ( Jenkins ) responsible to receive the developments from local and process it to production.

- Nobody can touch in prod. Only Jenkins entity can deploy to prod.

- GitHub is the chosen repository provider.

- The branch "master" is the main branch. This branch is looked. Nobody can do push directly to it ( even the owner ).

- Every new code to master needs a new pull request and reviews.

- The number of approvals is defined according the number of the elements, the seniority level of the team and the specification associated.

- There's a CODEOWNERS file in the project where git will lock at and only will allow to proceed with the deploy when all rules defined are accomplished.

## Technical details links

- [Production implementation AKA Adilia!](/case-studies/file/Y3MvMHNldHVwL1JFQURNRS5tZA==)

- [Local implementation AKA Ready to dev!](/case-studies/file/Y3MvMWJhc2ljLXNldHVwL1JFQURNRS5tZA==)

- [CI / CD implementation](/case-studies/file/Y3MvMi4xY2ktY2QvUkVBRE1FLm1k)

## Services, hosts and ports

- [Services](/details)

## Case studies

- [Implemented case studies in this project](/case-studies)

## Demonstration videos

- [Demonstration videos about implemented case studies in this project](https://www.youtube.com/@JGomes-dev/videos)
