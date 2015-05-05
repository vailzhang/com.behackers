
if [ -d "cook" ]
then
cd cook
git pull
cd ..
else
git clone https://github.com/hailongz/php.git cook
fi

if [ -d "org.hailong.configs" ]
then
svn update cook/org.hailong.configs
else
svn checkout http://svn.hailong.org:8082/Projects/qiezi/cook/trunk/php/org.hailong.configs cook/org.hailong.configs
fi

if [ -d "com.9vteam.cook" ]
then
svn update cook/com.9vteam.cook
else
svn checkout http://svn.hailong.org:8082/Projects/qiezi/cook/trunk/php/com.9vteam.cook cook/com.9vteam.cook
fi

if [ -d "com.9vteam.cook.admin" ]
then
svn update cook/com.9vteam.cook.admin
else
svn checkout http://svn.hailong.org:8082/Projects/qiezi/cook/trunk/php/com.9vteam.cook.admin cook/com.9vteam.cook.admin
fi
