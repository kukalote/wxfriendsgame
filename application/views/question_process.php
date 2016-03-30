<html>
<head> 
    <meta charset="utf8" />
    <title> 高手过招 </title>
<script>
var questions = <?php echo $questions;?>;
//console.log(questions);
function question(data)
{
    var t = this;
    this.source         = data;
    this.pointer        = 0;         //当前问题指针

    this.current        = function()
    {
        var current_question = this.source[this.pointer];
        this.title       = current_question.title;
        this.description = current_question.description;
        this.options     = current_question.options;
        return this;
    };
    this.next           = function()
    {
        this.pointer++;
        return this.current();
    };
    this.init           = function()
    {
        this.pointer = 0;
        return this.current();
    }

    this.title          = '';
    this.description    = '';
    this.options        = {};
}


var t = new question(questions);
console.log(t.init());
console.log(t.next());
console.log(t.next());

</script>
</head>

<body>
<div id="questions_1">
    <h1>第<span>1</span>题</h1>
    <div>
        <div class="question_title">标题</div>
        <div class="options">
            <a href="javascript::void(0);">选项1</a>
            <a href="javascript::void(0);">选项2</a>
            <a href="javascript::void(0);">选项3</a>
            <a href="javascript::void(0);">选项4</a>
        </div>
    </div>
</div>

</body>
</html>
