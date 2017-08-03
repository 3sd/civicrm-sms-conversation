# Create conversations

The first thing to do once you have installed SMS conversation is to define a conversation.

Select **SMS conversations** from the mailing menu to view the conversations screen.

![SMS conversations](./sms-conversation-menu-item.png)

This screen is where you will see all of your defined SMS conversations. Create your first conversation by clicking on **Create a new conversation**.

You will be prompted to name the conversation and enter your first question.

## Questions

Each conversation consist of a series of questions, which are the outbound SMS that are sent to your contacts.  Typically the contact will reply to your question with an answer, and you can carry out various **actions** depending on the answer they give you.

## Actions

Probably the most essential action is to ask another question. In this way you can create a series of questions and answers, thus making up a conversation.

Two other actions are currently available:

* Add to group, which allows you to add the contact to a group
* Record in field, which allows you to record the answer that they have given you in any core or custom contact field.

In the example below, we have created our first question and can now add an action by clicking on a link in the right hand column.

![SMS conversations](./sms-conversation-first-question.png)

## Matching

Often, you will only want to carry out an action if the answer matches a certain criteria. For example, if you ask the question "Would you like to be added to our newsletter?", then you would only want to add them to the newsletter if the reply was "Yes" (or something similar).

In the example below, we only ask question 2 if the answer to question 1 contains the word 'yes'.

![SMS conversations](./sms-conversation-answer-matches.png)

Matching is configured for each action and there are a few different matching types to choose from. Most are self explanatory and documented in the user interface (click on the question marks for more info).

If no matching action is found for a question, then the Invalid text will be sent.  This is useful, for example, when you are looking for an expected answer and want to repeat the question if you do not receive an answer that you were looking for (see the example above).

### Advanced mode

An advanced mode allows you to define a **regular expression** for the matching. This is useful if you want to configure more sophisticated pattern matching for national ID numbers or post codes or similar.

Here are a couple of examples:

Regular expression | Matches
:----------------- | :---------------------------------------------------------
^a                 | Starts with the character 'a'
\d{5}              | A five digit number
^[a-c]+$           | A string made up of the characters a b and c exclusively
x.\*y              | any string that contains the letters x and y in that order

Regular expressions are very powerful, but do have a bit of a learning curve. http://www.phpliveregex.com/ is a handy tool that allows you to experiment with regular expressions and see what they will and will not match.

## Branching

You may want to ask different questions depending on the answer to the previous question. To do this, you configure multiple 'go to question' actions for a question. In the following example, if someone says that they like music the best, we ask who their favourite band is, and if they say that they like photography the best, we ask who their favourite photograper is. For extra bonus points, we are recording the answer to the favourite band question in a custom field 'Favourite band'.

![SMS conversations](./sms-conversation-branching.png)

## Weight

Note that although you can configure your 'go to question' actions so that more than one matches an answer, SMS conversation will go to the first matching 'go to question' that it finds, and ignore any subsequent 'go to question' actions.

Here's an example to illustrate when this might be useful.

Lets say you want to find out what someone's favourite food is and what their favourite drink is. And lets say you are especially interested in pizza. If they say that their favourite food is pizza, you want to find out what topping they like but if they say anything else, you just want to go straight on to the favourite drink question, you can do that by configuring two 'go to question' actions on the favourite food question.

* If the answer 'contains pizza', *go to the question about toppings*
* If they answer is 'anything' *go to the question about favourite drinks*

In this example, if we reply with pizza, it will be matched and we'll ask the pizza question. If the order was the other way around, pizza would be matched by anything and SMS conversation would not do any further matching.

Next question actions will be evaluated in the order that they appear in the user interface. If you want to change the order, you can adjust the weight of the action. Actions with a heavier weight appear lower down.
