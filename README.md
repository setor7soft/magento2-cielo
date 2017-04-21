Módulo de integração da Cielo Api 3.0 para Magento 2.x
====================================================

---
Descrição
---------
---
Esse módulo permite ao cliente utilizar a solução de pagamentos da Cielo diretamente no seu checkout


Requisitos
----------
---
 - [Magento] Community 2.0 | 2.1
 - [PHP] 5.5.0+
 - [API-3.0-PHP]


Instalação
-----------
Navegue até o diretório raíz da sua instalação do Magento 2 e siga os seguintes passos:

1. Instale via packagist
   - ```composer require setor7/magento2-cielo```
       - Neste momento, podem ser solicitadas suas credenciais de autenticação do Magento. Caso tenha alguma dúvida, há uma descrição de como proceder nesse [link da documentação oficial](http://devdocs.magento.com/guides/v2.0/install-gde/prereq/connect-auth.html).
2. Execute os comandos:
   - ```php bin/magento setup:upgrade```
   - ```php bin/magento setup:static-content:deploy``` ou ```php bin/magento setup:static-content:deploy pt_BR```, de acordo com as configurações da sua loja.
3. Dê permissões as pastas var/ pub/
   - ```chmod 777 -R var/ pub/```


Atualização
-----------
É altamente recomendado que você tenha um ambiente de testes para validar alterações e atualizações antes de atualizar sua loja em produção. É recomendado também que seja feito um **backup** da sua loja e informações importantes antes de executar qualquer procedimento de atualização/instalação.

A atualização do módulo do cielo é feita através do **composer** e pode ser feita de diversas maneiras, de acordo com suas preferências. Uma forma é através dos comandos:
1. ```composer update setor7/magento2-cielo```
2. ```php bin/magento setup:upgrade```
3. ```php bin/magento setup:static-content:deploy``` ou ```php bin/magento setup:static-content:deploy pt_BR```, de acordo com as configurações da sua loja.

**Observações**
- Em alguns casos, o Magento não atualiza os arquivos estáticos gerados, podendo ser necessário atualizar os mesmos via interface administrativa, comandos do terminal ou removendo diretamente conteúdo da pasta *pub/static/frontend/Magento/seu_tema/seu_idioma/Setor7_Cielo*.
- Em seguida, executar novamente o comando ```php bin/magento setup:static-content:deploy``` ou ```bin/magento setup:static-content:deploy pt_BR```, de acordo com as configurações da sua loja.


Dúvidas?
----------
---
Caso tenha dúvidas ou precise de suporte crie uma issue.


Changelog
---------
0.0.1
- Versão beta para testes.
- Realizando vendas via cartão de crédito
- Realizando Captura de venda via admin
- Parcelamento de compras.


Contribuições
-------------
---
Achou e corrigiu um bug ou tem alguma feature em mente e deseja contribuir?

* Faça um fork.
* Adicione sua feature ou correção de bug.
* Envie um pull request no [GitHub].
* Obs.: O Pull Request não deve ser enviado para o branch master e sim para o branch correspondente a versão ou para a branch de desenvolvimento.
