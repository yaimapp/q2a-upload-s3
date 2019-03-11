require 'aws-sdk'
require 'parallel'
require 'benchmark'

the_bucket='38qa.net'
the_function='createThumbnail'
parallel = 100

count = 0
result = Benchmark.realtime do

  client = Aws::Lambda::Client.new()
  m = Mutex.new

  File.write('done.txt', '')
  Parallel.each(IO.readlines('todo.txt', chomp: true), in_threads: parallel) do |path|
    begin
      json = %Q|{"Records":[{"s3":{"bucket":{"name":"#{the_bucket}"},"object":{"key":"#{path}"}}}]}|
      res = client.invoke(function_name: the_function, payload: json)
      if res[:status_code] == 200
        begin
          m.lock
          File.write('done.txt', path + "\n", mode: 'a')
        ensure
          m.unlock
        end
      else
        puts "#{path} status:#{res[:status_code]}"
      end
    rescue => e
      puts path
      p e.message
    end
    count+=1
  end

end
puts "件数: #{count}件"
puts "時間: #{result}s"
