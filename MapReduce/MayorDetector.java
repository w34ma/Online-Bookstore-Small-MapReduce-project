import java.io.IOException;
import java.util.HashMap;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Collections;
import java.util.StringTokenizer;

import org.apache.hadoop.conf.Configured;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.Reducer.Context;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;
import org.apache.hadoop.util.Tool;
import org.apache.hadoop.util.ToolRunner;


public class MayorDetector extends Configured implements Tool{

	public static class MayorMapper extends Mapper<LongWritable, Text, IntWritable, IntWritable> {
		
		/*
		input key: LongWritable
        input value:
        output key: place id
        output value: "uid"
		*/
        private final static IntWritable one = new IntWritable(1);
        private IntWritable count = new IntWritable();
        
        public void map(LongWritable key, Text value, Context context) throws IOException, InterruptedException{
            String line = value.toString();
            String[] parts =line.split("\\|");
                int place = Integer.parseInt(parts[0]);
                int user = Integer.parseInt(parts[1]);
                context.write(new IntWritable(place),new IntWritable(user));
        }
	}
	
	public static class MayorReducer extends Reducer<IntWritable, IntWritable, IntWritable, Text> {
		/*
         input key: place id
         input value:user id
         output key: place id
         output value: "uid|count"
		*/
        public void reduce(IntWritable key, Iterable<IntWritable> values, Context context)throws IOException,InterruptedException{

            HashMap<Integer, Integer> hash = new HashMap<>();

            for(IntWritable value:values){
                if(!hash.containsKey(value.get())){
                    hash.put(value.get(),1);
                }else{
                    hash.put(value.get(), hash.get(value.get()) + 1);
                }
            }
            
            int max = Collections.max(hash.values());

            if(max >= 10){
            	int uid = 0x7fffffff;
                for (Entry<Integer, Integer> entry : hash.entrySet()) {
                    if (entry.getValue()==max) {
                    	if (uid > entry.getKey()) uid = entry.getKey();
                    }
                }
                Text result = new Text();
                result.set(uid + "|" + max);
                context.write(key,result);
            }
        }
	}
	
	
	@Override
	public int run(String[] args) throws Exception {
		
		Job job = new Job(getConf());		
		job.setJarByClass(MayorDetector.class);
		job.setJobName("MayorDetector");
		
		job.setMapOutputKeyClass(IntWritable.class);
		job.setMapOutputValueClass(IntWritable.class);
		job.setOutputKeyClass(IntWritable.class);
		job.setOutputValueClass(Text.class);
		
        job.setMapperClass(MayorMapper.class);
        job.setReducerClass(MayorReducer.class);

		job.setInputFormatClass(TextInputFormat.class);
		job.setOutputFormatClass(TextOutputFormat.class);
		FileInputFormat.setInputPaths(job, new Path(args[0]));
		FileOutputFormat.setOutputPath(job, new Path(args[1]));
		
		boolean success = job.waitForCompletion(true);
		
		return success ? 0 : 1;
	}
	
	/**
	 * @param args
	 * @throws Exception 
	 */
	public static void main(String[] args) throws Exception {
		int isSuccess = ToolRunner.run(new MayorDetector(), args);
		System.exit(isSuccess);
	}
}
